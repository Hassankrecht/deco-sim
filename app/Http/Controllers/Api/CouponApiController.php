<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Models\Checkout;
use App\Models\Coupon;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class CouponApiController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now('Africa/Luanda')->format('Y-m-d H:i:s');
        $showAll = $request->query('scope') === 'all';
        $user = $this->resolveBearerUser($request);

        $coupons = Coupon::query()
            ->where(function ($query) use ($user) {
                $query->where(function ($query) {
                    $query->whereNull('user_id')
                        ->whereIn('generated_for', ['manual', 'manuel']);
                });

                if ($user) {
                    $query->orWhere('user_id', $user->id);
                }
            })
            ->when(!$showAll, function ($query) use ($now) {
                $query->where('status', true)
                    ->where(function ($query) use ($now) {
                        $query->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
                    })
                    ->where(function ($query) use ($now) {
                        $query->whereNull('expiration_date')->orWhere('expiration_date', '>', $now);
                    })
                    ->where(function ($query) {
                        $query->where('usage_limit', '<=', 0)
                            ->orWhereColumn('used_count', '<', 'usage_limit');
                    });
            })
            ->orderByDesc('created_at')
            ->paginate($request->integer('per_page', 20));

        $usageCounts = collect();
        if ($user) {
            $usageCounts = Checkout::query()
                ->where('user_id', $user->id)
                ->whereNotNull('coupon_id')
                ->selectRaw('coupon_id, COUNT(*) as total')
                ->groupBy('coupon_id')
                ->pluck('total', 'coupon_id');
        }

        $coupons->getCollection()->transform(function (Coupon $coupon) use ($usageCounts, $user) {
            $coupon->setAttribute('user_used_count', (int) ($usageCounts[$coupon->id] ?? 0));
            $coupon->setAttribute('is_assigned_to_current_user', $this->isVisibleForUser($coupon, $user));
            return $coupon;
        });

        if (!$showAll) {
            $coupons->setCollection(
                $coupons->getCollection()->filter(function (Coupon $coupon) {
                    return (bool) $coupon->is_assigned_to_current_user
                        && (!$coupon->user_usage_limit
                            || $coupon->user_usage_limit <= 0
                            || (int) $coupon->user_used_count < $coupon->user_usage_limit);
                })->values()
            );
        }

        return CouponResource::collection($coupons);
    }

    private function resolveBearerUser(Request $request): ?User
    {
        $user = $request->user();
        if ($user instanceof User) {
            return $user;
        }

        $token = $request->bearerToken();
        if (!$token) {
            return null;
        }

        $accessToken = PersonalAccessToken::findToken($token);
        $tokenable = $accessToken?->tokenable;

        return $tokenable instanceof User ? $tokenable : null;
    }

    private function isVisibleForUser(Coupon $coupon, ?User $user): bool
    {
        if ($coupon->user_id) {
            return $user && (int) $coupon->user_id === (int) $user->id;
        }

        return in_array(strtolower((string) $coupon->generated_for), ['manual', 'manuel'], true);
    }
}
