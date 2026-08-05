<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Mail\OrderPlacedMail;
use App\Models\Checkout;
use App\Models\CheckoutItem;
use App\Models\Coupon;
use App\Models\Product;
use App\Services\PostpayCouponAssigner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class OrderApiController extends Controller
{
    /**
     * Display a listing of the authenticated user's orders.
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $orders = Checkout::with(['items.product.translations', 'coupon'])
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));
        return OrderResource::collection($orders);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'delivery_fee' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'coupon_code' => ['nullable', 'string', 'max:100'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['required', 'string', 'regex:/^\+244\d{9}$/'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'street_address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'order_note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['nullable', 'string', 'max:100'],
            'privacy_accepted' => ['accepted'],
            'source_platform' => ['nullable', 'in:web,android,ios'],
        ]);

        $productIds = collect($validated['items'])->pluck('product_id')->all();
        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');
        $deliveryFee = (float) ($validated['delivery_fee'] ?? 0);

        $subtotal = collect($validated['items'])->sum(function ($item) use ($products) {
            $product = $products->get($item['product_id']);
            $price = array_key_exists('price', $item) && $item['price'] !== null
                ? (float) $item['price']
                : (float) optional($product)->price;

            return $price * (int) $item['quantity'];
        });

        if (array_key_exists('subtotal', $validated) && $validated['subtotal'] !== null) {
            $subtotal = (float) $validated['subtotal'];
        }

        $couponCode = isset($validated['coupon_code']) ? trim($validated['coupon_code']) : null;

        $checkout = DB::transaction(function () use ($validated, $user, $products, $subtotal, $deliveryFee, $couponCode) {
            $coupon = $this->resolveCoupon($couponCode, $user->id, $subtotal);
            $discount = $coupon ? $this->calculateCouponDiscount($coupon, $subtotal) : 0;
            $total = max($subtotal + $deliveryFee - $discount, 0);
            $streetAddress = trim($validated['street_address'] ?? $validated['delivery_address']);
            $city = trim($validated['city'] ?? $user->town ?? '');

            $checkout = Checkout::create([
                'user_id' => $user->id,
                'coupon_id' => $coupon?->id,
                'name' => $validated['customer_name'],
                'email' => $user->email,
                'phone_number' => $validated['customer_phone'],
                'town' => $city,
                'country' => $user->country ?? 'Angola',
                'zipcode' => $user->zipcode ?? '',
                'address' => $streetAddress,
                'total_price' => $total,
                'total_before_discount' => $subtotal,
                'discount_amount' => $discount,
                'delivery_fee' => $deliveryFee,
                'payment_method' => $validated['payment_method'] ?? 'Cash on delivery',
                'order_note' => $validated['order_note'] ?? null,
                'source_platform' => $validated['source_platform'] ?? 'android',
                'status' => 'Pending',
            ]);

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);
                $price = array_key_exists('price', $item) && $item['price'] !== null
                    ? (float) $item['price']
                    : (float) optional($product)->price;
                $quantity = (int) $item['quantity'];

                CheckoutItem::create([
                    'checkout_id' => $checkout->id,
                    'product_id' => $item['product_id'],
                    'name' => optional($product)->title_localized ?? optional($product)->title ?? 'Product',
                    'image' => optional($product)->image,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total_price' => $price * $quantity,
                ]);
            }

            if ($coupon) {
                $coupon->increment('used_count');

                if ($coupon->usage_limit > 0 && $coupon->fresh()->used_count >= $coupon->usage_limit) {
                    $coupon->status = false;
                    $coupon->save();
                }
            }

            return $checkout;
        });

        app(PostpayCouponAssigner::class)->assign($user->id, (float) ($checkout->total_before_discount ?? $subtotal));
        $this->sendOrderEmails($checkout->load(['items', 'coupon', 'user']));

        return (new OrderResource($checkout->load(['items.product.translations', 'coupon'])))
            ->response()
            ->setStatusCode(201);
    }

    public function cancel($id)
    {
        $user = Auth::user();
        $order = Checkout::where('user_id', $user->id)->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if (in_array($order->status, ['Cancelled', 'Shipped', 'Paid'], true)) {
            return response()->json(['message' => 'This order cannot be cancelled.'], 422);
        }

        $order->status = 'Cancelled';

        if (array_key_exists('cancelled_at', $order->getAttributes())) {
            $order->cancelled_at = now();
        }

        $order->save();

        return new OrderResource($order->load(['items.product.translations', 'coupon']));
    }

    private function resolveCoupon(?string $couponCode, int $userId, float $subtotal): ?Coupon
    {
        if (!$couponCode) {
            return null;
        }

        $coupon = Coupon::where('code', $couponCode)->lockForUpdate()->first();

        if (!$coupon) {
            throw ValidationException::withMessages(['coupon_code' => 'Coupon not found.']);
        }

        $now = Carbon::now('Africa/Luanda');

        $isManualPublicCoupon = !$coupon->user_id && in_array(strtolower((string) $coupon->generated_for), ['manual', 'manuel'], true);
        $isUserCoupon = $coupon->user_id && (int) $coupon->user_id === $userId;

        if (!$isManualPublicCoupon && !$isUserCoupon) {
            throw ValidationException::withMessages(['coupon_code' => 'This coupon is not available for your account.']);
        }

        if (!$coupon->status) {
            throw ValidationException::withMessages(['coupon_code' => 'Coupon is inactive.']);
        }

        if ($coupon->starts_at && $now->lt(Carbon::parse($coupon->starts_at, 'Africa/Luanda'))) {
            throw ValidationException::withMessages(['coupon_code' => 'Coupon not started yet.']);
        }

        if ($coupon->expiration_date && $now->gte(Carbon::parse($coupon->expiration_date, 'Africa/Luanda'))) {
            throw ValidationException::withMessages(['coupon_code' => 'Coupon expired.']);
        }

        if ($coupon->usage_limit > 0 && $coupon->used_count >= $coupon->usage_limit) {
            throw ValidationException::withMessages(['coupon_code' => 'Coupon usage limit reached.']);
        }

        if ($coupon->user_usage_limit && $coupon->user_usage_limit > 0) {
            $alreadyUsed = Checkout::where('user_id', $userId)
                ->where('coupon_id', $coupon->id)
                ->count();

            if ($alreadyUsed >= $coupon->user_usage_limit) {
                throw ValidationException::withMessages(['coupon_code' => 'You have reached the usage limit for this coupon.']);
            }
        }

        if ($coupon->min_total && $subtotal < (float) $coupon->min_total) {
            throw ValidationException::withMessages(['coupon_code' => 'Order total is below minimum required.']);
        }

        return $coupon;
    }

    private function calculateCouponDiscount(Coupon $coupon, float $subtotal): float
    {
        $discount = $coupon->type === 'percent'
            ? round($subtotal * ((float) $coupon->value / 100), 2)
            : (float) $coupon->value;

        return min($discount, $subtotal);
    }

    private function sendOrderEmails(Checkout $checkout): void
    {
        try {
            Mail::to($checkout->email)->send(new OrderPlacedMail($checkout, false));
            Mail::to(config('mail.admin_address', 'h.krecht01@gmail.com'))->send(new OrderPlacedMail($checkout, true));
        } catch (\Throwable $e) {
            Log::warning('Flutter order email failed', [
                'checkout_id' => $checkout->id,
                'message' => $e->getMessage(),
            ]);
        }
    }
}





