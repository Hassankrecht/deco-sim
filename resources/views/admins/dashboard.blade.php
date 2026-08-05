{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container py-4">
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h5 class="mb-0 text-gold d-flex align-items-center gap-2">
        <i class="bi bi-speedometer2"></i> Analytics Dashboard
    </h5>
    <form class="d-flex flex-wrap align-items-center gap-2" method="GET">
        <select name="range" class="form-select form-select-sm w-auto" onchange="toggleCustom(this); this.form.submit();">
            <option value="last_7" {{ $range === 'last_7' ? 'selected' : '' }}>Last 7 days</option>
            <option value="last_30" {{ $range === 'last_30' ? 'selected' : '' }}>Last 30 days</option>
            <option value="last_month" {{ $range === 'last_month' ? 'selected' : '' }}>Last month</option>
            <option value="last_3m" {{ $range === 'last_3m' ? 'selected' : '' }}>Last 3 months</option>
            <option value="last_6m" {{ $range === 'last_6m' ? 'selected' : '' }}>Last 6 months</option>
            <option value="last_year" {{ $range === 'last_year' ? 'selected' : '' }}>Last year</option>
            <option value="custom" {{ $range === 'custom' ? 'selected' : '' }}>Custom</option>
        </select>
        <div id="customRange" class="d-flex gap-2 {{ $range === 'custom' ? '' : 'd-none' }}">
            <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
            <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
            <button class="btn btn-gold btn-sm">Apply</button>
        </div>
        <span class="badge bg-dark text-gold ms-2">
            {{ $startDate->format('Y-m-d') }} — {{ $endDate->format('Y-m-d') }}
        </span>
    </form>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-dark p-3 d-flex flex-column h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                    <i class="bi bi-receipt"></i>
                </div>
                <div>
                    <div class="text-muted small">Orders (range)</div>
                    <div class="fs-4 fw-bold text-gold">{{ $ordersRange ?? 0 }}</div>
                    <div class="small text-muted">Reg {{ $registeredCount ?? 0 }} / Guest {{ $guestCount ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-dark p-3 d-flex flex-column h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div>
                    <div class="text-muted small">Net revenue</div>
                    <div class="fs-4 fw-bold text-gold">{{ \App\Support\Currency::format($netRevenue ?? 0) }}</div>
                    <div class="small text-muted">Paid orders only</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-dark p-3 d-flex flex-column h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                    <i class="bi bi-bar-chart"></i>
                </div>
                <div>
                    <div class="text-muted small">AOV (avg order)</div>
                    <div class="fs-4 fw-bold text-gold">{{ \App\Support\Currency::format($aov ?? 0) }}</div>
                    <div class="small text-muted">With coupon {{ $ordersWithCouponCount ?? 0 }} / Without {{ $ordersWithoutCouponCount ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-dark p-3 d-flex flex-column h-100">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                    <i class="bi bi-flag"></i>
                </div>
                <div>
                    <div class="text-muted small">Statuses</div>
                    @php
                        $pending = $statusCountsRange['Pending'] ?? 0;
                        $paid = $statusCountsRange['Paid'] ?? 0;
                        $cancelled = $statusCountsRange['Cancelled'] ?? 0;
                    @endphp
                    <div class="fw-bold text-gold small">Pending {{ $pending }} · Paid {{ $paid }} · Cancelled {{ $cancelled }}</div>
                    @if(!empty($statusCountsRange))
                        @php
                            $topStatus = collect($statusCountsRange)->sortDesc()->keys()->first();
                            $topCount = collect($statusCountsRange)->sortDesc()->first();
                        @endphp
                        <div class="small text-muted">Top: {{ $topStatus }} ({{ $topCount }})</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-dark p-3 d-flex flex-row align-items-center gap-3">
            <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                <i class="bi bi-graph-up"></i>
            </div>
            <div>
                <div class="text-muted small">Visits (range)</div>
                <div class="fs-5 fw-bold text-gold">{{ $visitsRange }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-dark p-3 d-flex flex-row align-items-center gap-3">
            <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                <i class="bi bi-cursor"></i>
            </div>
            <div>
                <div class="text-muted small">Events/Clicks</div>
                <div class="fs-5 fw-bold text-gold">{{ $eventsRange }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-dark p-3 d-flex flex-row align-items-center gap-3">
            <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                <i class="bi bi-handbag"></i>
            </div>
            <div>
                <div class="text-muted small">Buy clicks</div>
                <div class="fs-5 fw-bold text-gold">{{ $buyClicks }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-dark p-3 d-flex flex-row align-items-center gap-3">
            <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                <i class="bi bi-box-seam"></i>
            </div>
            <div>
                <div class="text-muted small">Products</div>
                <div class="fs-5 fw-bold text-gold">{{ $productsCount }}</div>
                <div class="small text-muted">Projects: {{ $projectsCount }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-dark p-3 d-flex flex-row align-items-center gap-3">
            <div class="rounded-circle bg-dark text-gold d-flex align-items-center justify-content-center" style="width:46px;height:46px;">
                <i class="bi bi-envelope"></i>
            </div>
            <div>
                <div class="text-muted small">Unread Messages</div>
                <div class="fs-5 fw-bold text-gold">{{ $unreadMessagesCount ?? 0 }}</div>
                <div class="small text-muted"><a href="{{ route('admin.messages.index') }}" class="text-gold">View messages</a></div>
            </div>
        </div>
    </div>
</div>

<div class="card card-dark p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h5 class="mb-0" id="chartTitle">Orders (range)</h5>
            <small class="text-muted">Toggle to view orders or revenue</small>
        </div>
        <span class="badge bg-dark text-gold">{{ $startDate->format('Y-m-d') }} — {{ $endDate->format('Y-m-d') }}</span>
    </div>
    <div class="d-flex gap-2 mb-2">
        <button class="btn btn-sm btn-outline-gold" onclick="setSeries('orders')">Orders</button>
        <button class="btn btn-sm btn-outline-gold" onclick="setSeries('revenue')">Revenue</button>
    </div>
    <canvas id="trafficChart" height="120"></canvas>
</div>

@if(($pendingCount ?? 0) > 0 || ($cancelledCount ?? 0) > 0)
<div class="alert alert-warning">
    <div class="fw-bold mb-1">Attention</div>
    <div class="small mb-0">Pending: {{ $pendingCount ?? 0 }} · Cancelled: {{ $cancelledCount ?? 0 }} in selected range.</div>
</div>
@endif

@if(!empty($latestOrders) && $latestOrders->count())
<div class="card card-dark p-3 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h6 class="mb-0">Latest Orders</h6>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-gold">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table table-sm table-dark align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($latestOrders as $o)
                    <tr>
                        <td>#{{ $o->id }}</td>
                        <td>{{ $o->name }}</td>
                        <td>{{ $o->status }}</td>
                        <td>{{ \App\Support\Currency::format($o->total_price) }}</td>
                        <td>{{ \Carbon\Carbon::parse($o->created_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@if(isset($topActions) && $topActions->count())
<div class="card card-dark p-3">
    <h6 class="mb-3">Top Actions</h6>
    <ul class="list-group list-group-flush">
        @foreach($topActions as $action)
            <li class="list-group-item d-flex justify-content-between align-items-center bg-dark text-light">
                <span class="text-capitalize">{{ str_replace('_', ' ', $action->action) }}</span>
                <span class="badge bg-gold text-dark">{{ $action->total }}</span>
            </li>
        @endforeach
    </ul>
</div>
@endif</div>@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function toggleCustom(sel){
    const wrap = document.getElementById('customRange');
    if(!wrap) return;
    wrap.classList.toggle('d-none', sel.value !== 'custom');
}

const ctx = document.getElementById('trafficChart');
const labels = @json($visitsChart['labels'] ?? []);
const seriesMap = {
    visits: { label: 'Visits', color: '#c7954b', data: @json($visitsChart['data'] ?? []) },
    events: { label: 'Events/Clicks', color: '#6cb2eb', data: @json($eventsChart['data'] ?? []) },
    buy:    { label: 'Buy clicks', color: '#8bc34a', data: @json($buyChart['data'] ?? []) },
    orders: { label: 'Orders', color: '#f87171', data: @json($ordersChart['data'] ?? []) },
    revenue:{ label: 'Revenue', color: '#4ade80', data: @json($ordersChart['data'] ?? []) },
};

let activeSeries = 'orders';

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: seriesMap[activeSeries].label,
            data: seriesMap[activeSeries].data,
            borderColor: seriesMap[activeSeries].color,
            backgroundColor: hexToRgba(seriesMap[activeSeries].color, 0.18),
            borderWidth: 2,
            tension: 0.35,
            fill: true,
        }]
    },
    options: {
        plugins: { legend: { display: false }, tooltip: { enabled: true } },
        scales: {
            x: { ticks: { color: '#f1f5f9' }, grid: { color: 'rgba(255,255,255,0.05)' } },
            y: { beginAtZero: true, ticks: { color: '#f1f5f9' }, grid: { color: 'rgba(255,255,255,0.05)' } }
        }
    }
});

function hexToRgba(hex, alpha) {
    const c = hex.replace('#','');
    const bigint = parseInt(c, 16);
    const r = (bigint >> 16) & 255;
    const g = (bigint >> 8) & 255;
    const b = bigint & 255;
    return `rgba(${r},${g},${b},${alpha})`;
}

function setSeries(key) {
    if (!seriesMap[key]) return;
    activeSeries = key;
    chart.data.datasets[0].label = seriesMap[key].label;
    chart.data.datasets[0].data = seriesMap[key].data;
    chart.data.datasets[0].borderColor = seriesMap[key].color;
    chart.data.datasets[0].backgroundColor = hexToRgba(seriesMap[key].color, 0.18);
    chart.update();
    document.getElementById('chartTitle').textContent = seriesMap[key].label + ' (range)';
}

// تفعيل الافتراضي
setSeries('visits');
</script>
@endpush

