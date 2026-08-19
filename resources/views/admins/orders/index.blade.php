@extends('layouts.admin')

@section('content')
<div class="container py-4">
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-1 text-gold"><i class="bi bi-receipt me-2"></i>{{ __('admin.orders_management') }}</h5>
        <p class="text-muted small mb-0">{{ __('admin.orders_management_subtitle') }}</p>
    </div>
    <form class="d-flex flex-wrap align-items-center gap-2" method="GET" action="{{ route('admin.orders.index') }}" id="dateFilterForm">
        <select name="range" class="form-select form-select-sm w-auto" id="rangeSelect" onchange="handleRangeChange()">
            <option value="daily" {{ request('range')==='daily' ? 'selected' : '' }}>{{ __('admin.today') }}</option>
            <option value="weekly" {{ request('range')==='weekly' ? 'selected' : '' }}>{{ __('admin.last_7_days') }}</option>
            <option value="monthly" {{ request('range')==='monthly' ? 'selected' : '' }}>{{ __('admin.last_30_days') }}</option>
            <option value="3m" {{ request('range')==='3m' ? 'selected' : '' }}>{{ __('admin.last_3_months') }}</option>
            <option value="6m" {{ request('range')==='6m' ? 'selected' : '' }}>{{ __('admin.last_6_months') }}</option>
            <option value="1y" {{ request('range')==='1y' ? 'selected' : '' }}>{{ __('admin.last_year') }}</option>
            <option value="custom" {{ request('range')==='custom' ? 'selected' : '' }}>{{ __('admin.custom_range') }}</option>
        </select>
        <div id="customRange" class="d-flex gap-2 {{ request('range')==='custom' ? '' : 'd-none' }}">
            <input type="date" name="from" id="fromDate" value="{{ request('from', $dateFrom) }}" class="form-control form-control-sm">
            <span class="text-muted">{{ __('admin.to') }}</span>
            <input type="date" name="to" id="toDate" value="{{ request('to', $dateTo) }}" class="form-control form-control-sm">
            <button type="submit" class="btn btn-gold btn-sm">{{ __('admin.apply') }}</button>
        </div>
        <span class="badge bg-dark text-gold ms-auto">{{ $dateFrom }} → {{ $dateTo }}</span>
    </form>
</div>

<div class="mb-3">
    <a class="btn btn-outline-gold btn-sm" href="{{ route('admin.orders.export', request()->query()) }}">
        <i class="bi bi-download me-1"></i>{{ __('admin.export_csv') }}
    </a>
</div>

<div class="collapse show" id="filtersPanel">
    <div class="card card-dark p-3 mb-3">
    <form method="GET" id="ordersFilterForm" class="row g-2">
        <input type="hidden" name="range" value="{{ request('range') }}">
        <input type="hidden" name="from" value="{{ $dateFrom }}">
        <input type="hidden" name="to" value="{{ $dateTo }}">
        <input type="hidden" name="f_name" value="{{ $f['name'] ?? '' }}">
        <input type="hidden" name="f_email" value="{{ $f['email'] ?? '' }}">
        <input type="hidden" name="f_total" value="{{ $f['total'] ?? '' }}">
        <input type="hidden" name="f_discount" value="{{ $f['discount'] ?? '' }}">
        <input type="hidden" name="f_coupon" value="{{ $f['coupon'] ?? '' }}">
        <input type="hidden" name="f_account" value="{{ $f['account'] ?? '' }}">
        <input type="hidden" name="f_platform" value="{{ $f['platform'] ?? '' }}">
        
        <div class="col-md-3">
            <label class="form-label small mb-1">{{ __('admin.search') }}</label>
            <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="{{ __('admin.search_order_placeholder') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small mb-1">{{ __('admin.status') }}</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">{{ __('admin.all_statuses') }}</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ $filterStatus === $status ? 'selected' : '' }}>{{ $status }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">{{ __('admin.coupon') }}</label>
            <select name="has_coupon" class="form-select form-select-sm">
                <option value="">{{ __('admin.all_orders') }}</option>
                <option value="with" {{ ($hasCoupon ?? '')==='with' ? 'selected' : '' }}>{{ __('admin.with_coupon') }}</option>
                <option value="without" {{ ($hasCoupon ?? '')==='without' ? 'selected' : '' }}>{{ __('admin.without_coupon') }}</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small mb-1">{{ __('admin.sort') }}</label>
            <select name="sort" class="form-select form-select-sm">
                <option value="date_desc" {{ ($sort ?? '')==='date_desc' ? 'selected' : '' }}>{{ __('admin.newest') }}</option>
                <option value="date_asc" {{ ($sort ?? '')==='date_asc' ? 'selected' : '' }}>{{ __('admin.oldest') }}</option>
                <option value="total_desc" {{ ($sort ?? '')==='total_desc' ? 'selected' : '' }}>{{ __('admin.total_high_low') }}</option>
                <option value="total_asc" {{ ($sort ?? '')==='total_asc' ? 'selected' : '' }}>{{ __('admin.total_low_high') }}</option>
            </select>
        </div>
        <div class="col-md-2 d-flex align-items-end gap-2">
            <button class="btn btn-sm btn-gold"><i class="bi bi-search"></i> {{ __('admin.apply') }}</button>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-secondary">{{ __('admin.reset') }}</a>
        </div>
    </form>
    </div>
</div>

@php
    $activeFilters = [];
    if($search) $activeFilters[] = __('admin.search') . ': ' . $search;
    if($filterStatus) $activeFilters[] = __('admin.status') . ': ' . $filterStatus;
    if($hasCoupon === 'with') $activeFilters[] = __('admin.with_coupon');
    if($hasCoupon === 'without') $activeFilters[] = __('admin.without_coupon');
    if(!empty($f['platform'])) $activeFilters[] = __('admin.platform') . ': ' . $f['platform'];
    if($dateFrom || $dateTo) $activeFilters[] = __('admin.date_range');
@endphp
@if(count($activeFilters) > 0)
<div class="mb-3 d-flex flex-wrap gap-2">
    @foreach($activeFilters as $filter)
        <span class="badge bg-secondary">{{ $filter }}</span>
    @endforeach
    <a href="{{ route('admin.orders.index') }}" class="badge bg-danger text-decoration-none">{{ __('admin.clear_all') }}</a>
</div>
@endif

<div class="row g-3 mb-3">
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">{{ __('admin.orders') }}</div>
            <div class="fw-bold fs-5 text-gold">{{ $totalOrders }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">{{ __('admin.customers') }}</div>
            <div class="fw-bold fs-5 text-gold">{{ $customersCount }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">{{ __('admin.with_coupon') }}</div>
            <div class="fw-bold fs-5 text-gold">{{ $withCouponCount }}</div>
            <div class="text-muted small">{{ __('admin.distinct_coupons') }}: {{ $distinctCoupons }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">{{ __('admin.discount_total') }}</div>
            <div class="fw-bold fs-5 text-gold">{{ \App\Support\Currency::format($discountSum ?? 0) }}</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="card card-dark p-3 h-100">
            <div class="text-muted small">{{ __('admin.total_amount') }}</div>
            <div class="fw-bold fs-5 text-gold">{{ \App\Support\Currency::format($totalAmount ?? 0) }}</div>
        </div>
    </div>
    @foreach($statuses as $s)
        <div class="col-md-2 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ $s }}</div>
                <div class="fw-bold fs-5 text-gold">{{ $statusCounts[$s] ?? 0 }}</div>
            </div>
        </div>
    @endforeach
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="alert alert-danger small">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Bulk Actions Bar -->
<div id="bulkActionsBar" class="card card-dark p-3 mb-3 d-none">
    <div class="d-flex align-items-center gap-3">
        <span class="text-gold"><strong id="selectedCount">0</strong> {{ __('admin.selected') }}</span>
        <select id="bulkAction" class="form-select form-select-sm w-auto">
            <option value="">{{ __('admin.bulk_action') }}</option>
            @foreach($statuses as $status)
                <option value="status_{{ $status }}">{{ __('admin.status_change') }}: {{ $status }}</option>
            @endforeach
            <option value="delete">{{ __('admin.delete_selected') }}</option>
        </select>
        <button type="button" class="btn btn-sm btn-gold" onclick="executeBulkAction()">{{ __('admin.apply') }}</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSelection()">{{ __('admin.clear') }}</button>
    </div>
</div>

<div class="card card-dark">
    <style>
        /* Collapsible table styles */
        .order-row {
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
            cursor: pointer;
        }
        .order-row:hover {
            background-color: rgba(199, 149, 75, 0.15) !important;
        }
        .order-row.table-active {
            background-color: rgba(199, 149, 75, 0.25) !important;
            border-left-color: #c7954b !important;
        }

        /* Status-based row colors */
        .order-row.status-paid {
            background-color: rgba(34, 197, 94, 0.08);
        }
        .order-row.status-paid:hover {
            background-color: rgba(34, 197, 94, 0.15) !important;
        }
        .order-row.status-pending {
            background-color: rgba(251, 191, 36, 0.08);
        }
        .order-row.status-pending:hover {
            background-color: rgba(251, 191, 36, 0.15) !important;
        }
        .order-row.status-shipped {
            background-color: rgba(59, 130, 246, 0.08);
        }
        .order-row.status-shipped:hover {
            background-color: rgba(59, 130, 246, 0.15) !important;
        }
        .order-row.status-cancelled, .order-row.status-refunded {
            background-color: rgba(239, 68, 68, 0.08);
        }
        .order-row.status-cancelled:hover, .order-row.status-refunded:hover {
            background-color: rgba(239, 68, 68, 0.15) !important;
        }

        /* Expanded details */
        .expand-details {
            border: 1px solid rgba(199, 149, 75, 0.3);
        }
        .expand-details .bg-dark {
            background-color: #0d1117 !important;
            border-left: 3px solid #c7954b;
        }
        .toggle-icon {
            transition: transform 0.3s ease;
            color: #c7954b;
        }
    </style>
    <div class="table-responsive">
        <table class="table table-sm table-dark align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 35px;">
                        <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    <th style="width: 35px;"></th>
                    <th style="width: 80px;">{{ __('admin.hash') }}</th>
                    <th>{{ __('admin.customer') }}
                        @if(!empty($facets['names']))
                        <div class="dropdown d-inline float-end">
                            <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown"><i class="bi bi-funnel"></i></button>
                            <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                <a href="#" class="dropdown-item facet-option" data-field="f_name" data-value="">{{ __('admin.all') }}</a>
                                @foreach($facets['names'] as $item)
                                    @if($item->value)
                                        <a href="#" class="dropdown-item facet-option" data-field="f_name" data-value="{{ $item->value }}">{{ $item->value }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </th>
                    <th>{{ __('admin.status') }}</th>
                    <th>{{ __('admin.platform') }}
                        @if(!empty($facets['platforms']))
                        <div class="dropdown d-inline float-end">
                            <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="Filter platform"><i class="bi bi-funnel"></i></button>
                            <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                <a href="#" class="dropdown-item facet-option" data-field="f_platform" data-value="">{{ __('admin.all') }}</a>
                                @foreach($facets['platforms'] as $item)
                                    @php
                                        $platformValue = $item->value ?? 'unknown';
                                        $platformLabel = match($platformValue) {
                                            'web' => __('admin.website'),
                                            'android' => __('admin.android_app'),
                                            'ios' => __('admin.ios_app'),
                                            default => __('admin.unknown'),
                                        };
                                    @endphp
                                    <a href="#" class="dropdown-item facet-option" data-field="f_platform" data-value="{{ $platformValue }}">{{ $platformLabel }} <span class="text-light">({{ $item->count }})</span></a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </th>
                    <th>{{ __('admin.total') }}</th>
                    <th>{{ __('admin.created') }}</th>
                    <th style="width: 50px;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $statusLower = strtolower($order->status ?? '');
                        $statusClass = 'secondary';
                        $statusColor = 'secondary';
                        
                        if(str_contains($statusLower,'paid')) {
                            $statusColor = 'success';
                            $statusClass = 'paid';
                        } elseif(str_contains($statusLower,'pending')) {
                            $statusColor = 'warning';
                            $statusClass = 'pending';
                        } elseif(str_contains($statusLower,'ship')) {
                            $statusColor = 'info';
                            $statusClass = 'shipped';
                        } elseif(str_contains($statusLower,'cancel')) {
                            $statusColor = 'danger';
                            $statusClass = 'cancelled';
                        } elseif(str_contains($statusLower,'refund')) {
                            $statusColor = 'danger';
                            $statusClass = 'refunded';
                        }
                    @endphp
                    <tr class="order-row status-{{ $statusClass }}" data-order-id="{{ $order->id }}">
                        <td onclick="event.stopPropagation();">
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $order->id }}">
                        </td>
                        <td class="expand-toggle">
                            <i class="bi bi-chevron-right toggle-icon"></i>
                        </td>
                        <td><strong>#{{ $order->id }}</strong></td>
                        <td>{{ $order->name }}</td>
                        <td><span class="badge bg-{{ $statusColor }}">{{ $order->status }}</span></td>
                        <td><span class="badge bg-dark text-gold">{{ $order->source_platform ?: 'unknown' }}</span></td>
                        <td><strong>{{ \App\Support\Currency::format($order->total_price) }}</strong></td>
                        <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td onclick="event.stopPropagation();">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-gold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('admin.orders.show', $order) }}"><i class="bi bi-eye me-2"></i>{{ __('admin.view') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    @foreach($statuses as $status)
                                        <li>
                                            <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="{{ $status }}">
                                                <button type="submit" class="dropdown-item {{ $order->status === $status ? 'active' : '' }}">
                                                    {{ __('admin.status') }}: {{ $status }}
                                                </button>
                                            </form>
                                        </li>
                                    @endforeach
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('{{ __('admin.delete_order_confirm') }}');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i>{{ __('admin.delete') }}</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <!-- Expanded Details Row -->
                    <tr class="expand-details d-none" data-order-id="{{ $order->id }}">
                        <td colspan="9">
                            <div class="p-3 bg-dark rounded">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <h6 class="text-gold mb-2"><i class="bi bi-person-fill me-2"></i>{{ __('admin.customer_details') }}</h6>
                                        <dl class="row mb-0 small">
                                            <dt class="col-sm-4">{{ __('admin.email') }}:</dt>
                                            <dd class="col-sm-8">{{ $order->email }}</dd>
                                            
                                            <dt class="col-sm-4">{{ __('admin.phone') }}:</dt>
                                            <dd class="col-sm-8">{{ $order->phone_number ?? '—' }}</dd>
                                        </dl>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-gold mb-2"><i class="bi bi-cash-coin me-2"></i>{{ __('admin.payment_details') }}</h6>
                                        <dl class="row mb-0 small">
                                            <dt class="col-sm-4">{{ __('admin.total') }}:</dt>
                                            <dd class="col-sm-8"><strong class="text-success">{{ \App\Support\Currency::format($order->total_price) }}</strong></dd>
                                            
                                            <dt class="col-sm-4">{{ __('admin.discount') }}:</dt>
                                            <dd class="col-sm-8"><span class="text-danger">{{ \App\Support\Currency::format($order->discount_amount ?? 0) }}</span></dd>
                                            
                                            <dt class="col-sm-4">{{ __('admin.coupon') }}:</dt>
                                            <dd class="col-sm-8">{{ $order->coupon?->code ?? '—' }}</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">{{ __('admin.no_orders_found') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-3">
        {{ $orders->links() }}
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('ordersFilterForm');
    
    // Facet filtering
    document.querySelectorAll('.facet-option').forEach(opt => {
        opt.addEventListener('click', (e) => {
            e.preventDefault();
            const field = opt.dataset.field;
            const value = opt.dataset.value ?? '';
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.value = value;
                form.submit();
            }
        });
    });

    // Collapsible rows
    const orderRows = document.querySelectorAll('.order-row');
    orderRows.forEach(row => {
        const toggleIcon = row.querySelector('.toggle-icon');
        const orderId = row.dataset.orderId;
        const expandDetailsRow = document.querySelector(`.expand-details[data-order-id="${orderId}"]`);

        row.addEventListener('click', (e) => {
            // Don't expand if clicking checkbox or dropdown
            if (e.target.closest('.order-checkbox') || e.target.closest('.dropdown')) return;
            
            const isExpanded = !expandDetailsRow.classList.contains('d-none');
            
            if (isExpanded) {
                expandDetailsRow.classList.add('d-none');
                toggleIcon.classList.remove('bi-chevron-down');
                toggleIcon.classList.add('bi-chevron-right');
                row.classList.remove('table-active');
            } else {
                expandDetailsRow.classList.remove('d-none');
                toggleIcon.classList.remove('bi-chevron-right');
                toggleIcon.classList.add('bi-chevron-down');
                row.classList.add('table-active');
            }
        });
    });

    // Select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    const orderCheckboxes = document.querySelectorAll('.order-checkbox');
    const bulkActionsBar = document.getElementById('bulkActionsBar');
    const selectedCountSpan = document.getElementById('selectedCount');

    selectAllCheckbox?.addEventListener('change', function() {
        orderCheckboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActionsBar();
    });

    orderCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateBulkActionsBar);
    });

    function updateBulkActionsBar() {
        const selectedCount = document.querySelectorAll('.order-checkbox:checked').length;
        selectedCountSpan.textContent = selectedCount;
        
        if (selectedCount > 0) {
            bulkActionsBar.classList.remove('d-none');
        } else {
            bulkActionsBar.classList.add('d-none');
        }
        
        // Update select all checkbox state
        const allChecked = selectedCount === orderCheckboxes.length && selectedCount > 0;
        selectAllCheckbox.checked = allChecked;
        selectAllCheckbox.indeterminate = selectedCount > 0 && !allChecked;
    }
});

function clearSelection() {
    document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    document.getElementById('bulkActionsBar').classList.add('d-none');
}

function handleRangeChange() {
    const range = document.getElementById('rangeSelect').value;
    const customRange = document.getElementById('customRange');
    const fromDate = document.getElementById('fromDate');
    const toDate = document.getElementById('toDate');
    
    if (range === 'custom') {
        customRange.classList.remove('d-none');
    } else {
        customRange.classList.add('d-none');
        // Clear custom date inputs so they don't override the preset range
        fromDate.value = '';
        toDate.value = '';
        // Submit the form immediately with the selected range
        document.getElementById('dateFilterForm').submit();
    }
}

function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    if (!action) {
        alert('{{ __('admin.please_select_action') }}');
        return;
    }

    const selectedIds = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
    if (selectedIds.length === 0) {
        alert('{{ __('admin.no_orders_selected') }}');
        return;
    }

    if (action === 'delete') {
        let deleteMsg = '{{ __('admin.delete_n_orders') }}'.replace(':count', selectedIds.length);
        if (!confirm(deleteMsg)) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.orders.bulk-delete") }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'ids';
        idsInput.value = JSON.stringify(selectedIds);
        form.appendChild(idsInput);
        
        document.body.appendChild(form);
        form.submit();
    } else if (action.startsWith('status_')) {
        const status = action.replace('status_', '');
        let statusMsg = '{{ __('admin.change_status_n_orders_to') }}'.replace(':count', selectedIds.length).replace(':status', status);
        if (!confirm(statusMsg)) return;
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.orders.bulk-update") }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'ids';
        idsInput.value = JSON.stringify(selectedIds);
        form.appendChild(idsInput);
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
</div>
@endsection
