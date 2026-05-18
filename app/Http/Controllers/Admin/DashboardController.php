<?php

// app/Http/Controllers/Admin/DashboardController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\Project;
use App\Models\Product;
use App\Models\PageEvent;
use App\Models\PageVisit;
use App\Models\ContactMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $ordersCount   = Checkout::count();
        $projectsCount = Project::count();
        $productsCount = Product::count();
        $unreadMessagesCount = ContactMessage::where('is_read', false)->count();

        // نطاقات الزمن
        $range = request('range', 'last_7');
        $from  = request('from');
        $to    = request('to');

        [$startDate, $endDate] = $this->resolveRange($range, $from, $to);

        $visitsQuery = PageVisit::whereBetween('created_at', [$startDate, $endDate]);
        $eventsQuery = PageEvent::whereBetween('created_at', [$startDate, $endDate]);

        $visitsToday   = PageVisit::whereDate('created_at', today())->count();
        $eventsToday   = PageEvent::whereDate('created_at', today())->count();

        $visitsRange   = (clone $visitsQuery)->count();
        $eventsRange   = (clone $eventsQuery)->count();
        $buyClicks     = (clone $eventsQuery)->where('action', 'buy_click')->count();
        $ordersQueryRange = Checkout::whereBetween('created_at', [$startDate, $endDate]);
        $ordersRange   = (clone $ordersQueryRange)->count();

        $ordersWithCouponCount = (clone $ordersQueryRange)->whereNotNull('coupon_id')->count();
        $ordersWithoutCouponCount = (clone $ordersQueryRange)->whereNull('coupon_id')->count();
        $revenueWithCoupon = (clone $ordersQueryRange)->whereNotNull('coupon_id')->sum('total_price');
        $revenueWithoutCoupon = (clone $ordersQueryRange)->whereNull('coupon_id')->sum('total_price');
        $discountTotal = (clone $ordersQueryRange)->sum('discount_amount');
        $refundTotal   = (clone $ordersQueryRange)->sum('refund_amount');
        
        // Net Revenue: ONLY PAID ORDERS (paid/completed status)
        $netRevenue = Checkout::whereBetween('created_at', [$startDate, $endDate])
            ->whereIn(DB::raw("LOWER(status)"), ['paid', 'completed'])
            ->sum('total_price');
        
        $registeredCount = (clone $ordersQueryRange)->whereNotNull('user_id')->where('user_id','>',0)->count();
        $guestCount      = (clone $ordersQueryRange)->whereNull('user_id')->orWhere('user_id',0)->count();
        $aov = $ordersRange ? $netRevenue / $ordersRange : 0;
        $statusCountsRange = (clone $ordersQueryRange)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total','status');
        $pendingCount = $statusCountsRange['Pending'] ?? 0;
        $cancelledCount = $statusCountsRange['Cancelled'] ?? 0;
        $latestOrders = Checkout::orderByDesc('created_at')
            ->limit(5)
            ->get(['id','name','total_price','status','created_at']);

        $grouping = $this->decideGrouping($startDate, $endDate);
        $periods  = $this->buildPeriods($startDate, $endDate, $grouping);

        // تجميع الزيارات
        $visitSeries = collect($periods)->mapWithKeys(fn ($p) => [$p['key'] => 0])->toArray();
        foreach ($visitsQuery->get(['created_at']) as $visit) {
            $key = $this->periodKey($visit->created_at, $grouping);
            if (isset($visitSeries[$key])) {
                $visitSeries[$key]++;
            }
        }

        // تجميع الأحداث
        $eventSeries = collect($periods)->mapWithKeys(fn ($p) => [$p['key'] => 0])->toArray();
        foreach ($eventsQuery->get(['created_at']) as $event) {
            $key = $this->periodKey($event->created_at, $grouping);
            if (isset($eventSeries[$key])) {
                $eventSeries[$key]++;
            }
        }

        // تجميع buy_click
        $buySeries = collect($periods)->mapWithKeys(fn ($p) => [$p['key'] => 0])->toArray();
        foreach ((clone $eventsQuery)->where('action', 'buy_click')->get(['created_at']) as $event) {
            $key = $this->periodKey($event->created_at, $grouping);
            if (isset($buySeries[$key])) {
                $buySeries[$key]++;
            }
        }

        // تجميع الطلبات
        $orderSeries = collect($periods)->mapWithKeys(fn ($p) => [$p['key'] => 0])->toArray();
        foreach ((clone $ordersQueryRange)->get(['created_at']) as $order) {
            $key = $this->periodKey($order->created_at, $grouping);
            if (isset($orderSeries[$key])) {
                $orderSeries[$key]++;
            }
        }

        $visitsChart = [
            'labels' => collect($periods)->pluck('label')->toArray(),
            'data'   => array_values($visitSeries),
        ];
        $eventsChart = [
            'labels' => collect($periods)->pluck('label')->toArray(),
            'data'   => array_values($eventSeries),
        ];
        $buyChart = [
            'labels' => collect($periods)->pluck('label')->toArray(),
            'data'   => array_values($buySeries),
        ];
        $ordersChart = [
            'labels' => collect($periods)->pluck('label')->toArray(),
            'data'   => array_values($orderSeries),
        ];

        $topActions = (clone $eventsQuery)
            ->select('action', DB::raw('COUNT(*) as total'))
            ->groupBy('action')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('admins.dashboard', compact(
            'ordersCount',
            'projectsCount',
            'productsCount',
            'unreadMessagesCount',
            'visitsToday',
            'eventsToday',
            'visitsRange',
            'eventsRange',
            'buyClicks',
            'ordersRange',
            'ordersWithCouponCount',
            'ordersWithoutCouponCount',
            'revenueWithCoupon',
            'revenueWithoutCoupon',
            'discountTotal',
            'refundTotal',
            'netRevenue',
            'aov',
            'registeredCount',
            'guestCount',
            'statusCountsRange',
            'pendingCount',
            'cancelledCount',
            'latestOrders',
            'visitsChart',
            'eventsChart',
            'buyChart',
            'ordersChart',
            'topActions',
            'range',
            'startDate',
            'endDate'
        ));
    }

    private function resolveRange(string $range, ?string $from, ?string $to): array
    {
        $today = Carbon::today()->endOfDay();
        $start = Carbon::today()->subDays(6)->startOfDay();
        $end   = $today;

        switch ($range) {
            case 'today':
                $start = Carbon::today()->startOfDay();
                $end   = Carbon::today()->endOfDay();
                break;
            case 'last_7':
                $start = Carbon::today()->subDays(6)->startOfDay();
                break;
            case 'last_30':
                $start = Carbon::today()->subDays(29)->startOfDay();
                break;
            case 'last_month':
                $start = Carbon::today()->subMonth()->startOfMonth();
                $end   = Carbon::today()->subMonth()->endOfMonth();
                break;
            case 'last_3m':
                $start = Carbon::today()->subMonthsNoOverflow(3)->startOfMonth();
                break;
            case 'last_6m':
                $start = Carbon::today()->subMonthsNoOverflow(6)->startOfMonth();
                break;
            case 'last_year':
                $start = Carbon::today()->subYear()->startOfDay();
                break;
            case 'custom':
                if ($from && $to) {
                    $start = Carbon::parse($from)->startOfDay();
                    $end   = Carbon::parse($to)->endOfDay();
                    if ($start->greaterThan($end)) {
                        [$start, $end] = [$end, $start];
                    }
                }
                break;
        }

        return [$start, $end];
    }

    private function decideGrouping(Carbon $start, Carbon $end): string
    {
        $days = $start->diffInDays($end);
        if ($days <= 35) {
            return 'day';
        }
        if ($days <= 180) {
            return 'week';
        }
        return 'month';
    }

    private function buildPeriods(Carbon $start, Carbon $end, string $grouping): array
    {
        $periods = [];
        $cursor = $start->copy();

        if ($grouping === 'day') {
            while ($cursor->lte($end)) {
                $periods[] = [
                    'key'   => $cursor->format('Y-m-d'),
                    'label' => $cursor->format('d M'),
                ];
                $cursor->addDay();
            }
        } elseif ($grouping === 'week') {
            $cursor->startOfWeek();
            while ($cursor->lte($end)) {
                $periods[] = [
                    'key'   => $cursor->format('Y-m-d'),
                    'label' => 'أسبوع ' . $cursor->format('W'),
                ];
                $cursor->addWeek();
            }
        } else { // month
            $cursor->startOfMonth();
            while ($cursor->lte($end)) {
                $periods[] = [
                    'key'   => $cursor->format('Y-m-01'),
                    'label' => $cursor->translatedFormat('M Y'),
                ];
                $cursor->addMonth();
            }
        }

        return $periods;
    }

    private function periodKey($date, string $grouping): string
    {
        $c = $date instanceof Carbon ? $date : Carbon::parse($date);
        return match ($grouping) {
            'day'   => $c->format('Y-m-d'),
            'week'  => $c->startOfWeek()->format('Y-m-d'),
            default => $c->format('Y-m-01'),
        };
    }
}
