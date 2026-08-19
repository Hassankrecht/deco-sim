@extends('layouts.admin')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1 text-gold"><i class="bi bi-table me-2"></i>{{ __('admin.advanced_orders_report') }}</h5>
            <p class="text-muted small mb-0">{{ __('admin.orders_report_subtitle') }}</p>
        </div>
        <form class="d-flex flex-wrap align-items-center gap-2" method="GET" action="{{ route('admin.reports.orders.index') }}" id="dateFilterForm">
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
                <input type="date" name="from" id="fromDate" value="{{ request('from', $from) }}" class="form-control form-control-sm">
                <span class="text-muted">{{ __('admin.to') }}</span>
                <input type="date" name="to" id="toDate" value="{{ request('to', $to) }}" class="form-control form-control-sm">
                <button type="submit" class="btn btn-gold btn-sm">{{ __('admin.apply') }}</button>
            </div>
            <span class="badge bg-dark text-gold ms-auto">{{ $from }} → {{ $to }}</span>
        </form>
    </div>

    <div class="mb-3">
        <a class="btn btn-outline-gold btn-sm" href="{{ route('admin.reports.orders.export', request()->query()) }}">
            <i class="bi bi-download me-1"></i>{{ __('admin.export_csv') }}
        </a>
    </div>

    <form id="orderFilterForm" class="row g-2 mb-3" method="GET" action="{{ route('admin.reports.orders.index') }}">
        <!-- Preserve all current filter parameters -->
        <input type="hidden" name="range" value="{{ request('range') }}">
        <input type="hidden" name="from" value="{{ $from }}">
        <input type="hidden" name="to" value="{{ $to }}">
        <input type="hidden" name="account" value="{{ request('account') }}">
        <input type="hidden" name="order_id" value="{{ request('order_id') }}">
        <input type="hidden" name="before_value" value="{{ request('before_value') }}">
        <input type="hidden" name="discount_value" value="{{ request('discount_value') }}">
        <input type="hidden" name="min_total" value="{{ request('min_total') }}">
        <input type="hidden" name="refund_value" value="{{ request('refund_value') }}">
        
        <div class="row g-2 mb-3">
            <div class="col-md-3">
                <label class="form-label small mb-1">{{ __('admin.status') }}</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">{{ __('admin.all_statuses') }}</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st }}" {{ request('status')===$st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">{{ __('admin.platform') }}</label>
                <select name="source_platform" class="form-select form-select-sm">
                    <option value="">{{ __('admin.all_platforms') }}</option>
                    <option value="web" {{ request('source_platform')==='web'?'selected':'' }}>{{ __('admin.website') }}</option>
                    <option value="android" {{ request('source_platform')==='android'?'selected':'' }}>{{ __('admin.android_app') }}</option>
                    <option value="ios" {{ request('source_platform')==='ios'?'selected':'' }}>{{ __('admin.ios_app') }}</option>
                    <option value="unknown" {{ request('source_platform')==='unknown'?'selected':'' }}>{{ __('admin.unknown') }}</option>
                </select>
            </div>


            <div class="col-md-3">
                <label class="form-label small mb-1">{{ __('admin.discount') }}</label>
                <select name="discount_filter" class="form-select form-select-sm">
                    <option value="">{{ __('admin.all') }}</option>
                    <option value="with" {{ request('discount_filter')==='with'?'selected':'' }}>{{ __('admin.with_discount') }}</option>
                    <option value="without" {{ request('discount_filter')==='without'?'selected':'' }}>{{ __('admin.without_discount') }}</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">{{ __('admin.refund') }}</label>
                <select name="refund_filter" class="form-select form-select-sm">
                    <option value="">{{ __('admin.all') }}</option>
                    <option value="yes" {{ request('refund_filter')==='yes'?'selected':'' }}>{{ __('admin.yes') }}</option>
                    <option value="no" {{ request('refund_filter')==='no'?'selected':'' }}>{{ __('admin.no') }}</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small mb-1">{{ __('admin.coupon_code') }}</label>
                <input type="text" name="coupon_code" class="form-control form-control-sm" value="{{ request('coupon_code') }}" placeholder="Search coupon">
            </div>

            <div class="col-md-4">
                <label class="form-label small mb-1">{{ __('admin.customer_email') }}</label>
                <select name="user_search" class="form-select form-select-sm">
                    <option value="">{{ __('admin.all_customers') }}</option>
                    @if(!empty($facets['emails']))
                        @foreach($facets['emails'] as $item)
                            @php $label = ($item->value ?? '—'); @endphp
                            <option value="{{ $item->value }}" {{ request('user_search')==$item->value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-gold w-100" type="submit">{{ __('admin.apply') }}</button>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="{{ route('admin.reports.orders.index') }}" class="btn btn-outline-secondary w-100">{{ __('admin.reset') }}</a>
            </div>
        </div>
    </form>

    @if(isset($summary))
    <!-- KPI CARDS ROW 1: Core Metrics -->
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-muted small">{{ __('admin.total_orders') }}</div>
                    @if(!is_null($growthOrders))
                        <span class="badge {{ $growthOrders >=0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $growthOrders >=0 ? '↑' : '↓' }} {{ number_format(abs($growthOrders),1) }}%
                        </span>
                    @endif
                </div>
                <h4 class="mb-0 text-gold">{{ $summary['orders'] ?? 0 }}</h4>
                <small class="text-muted">{{ __('admin.total_orders') }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-muted small">{{ __('admin.net_revenue') }}</div>
                    @if(!is_null($growthRevenue))
                        <span class="badge {{ $growthRevenue >=0 ? 'bg-success' : 'bg-danger' }}">
                            {{ $growthRevenue >=0 ? '↑' : '↓' }} {{ number_format(abs($growthRevenue),1) }}%
                        </span>
                    @endif
                </div>
                <h4 class="mb-0 text-gold">${{ number_format($summary['net'] ?? 0,2) }}</h4>
                <small class="text-muted">{{ __('admin.total_order_revenue') }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.ado') }}</div>
                <h4 class="mb-0 text-gold">{{ number_format($avgDailyOrders ?? 0,1) }}</h4>
                <small class="text-muted">{{ __('admin.average_daily_orders') }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.total_discounts') }}</div>
                <h4 class="mb-0 text-gold">${{ number_format($summary['discounts'] ?? 0,2) }}</h4>
                <small class="text-muted">{{ __('admin.coupon_savings') }}</small>
            </div>
        </div>
    </div>

    <!-- KPI CARDS ROW 2: Customer Segmentation -->
    <div class="row g-3 mb-3">
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.registered_users') }}</div>
                <h5 class="mb-0 text-gold">{{ $summary['registered'] ?? 0 }}</h5>
                <small class="text-muted">{{ __('admin.with_account') }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.guest_users') }}</div>
                <h5 class="mb-0 text-gold">{{ $summary['guests'] ?? 0 }}</h5>
                <small class="text-muted">{{ __('admin.without_account') }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.with_coupon') }}</div>
                <h5 class="mb-0 text-gold">{{ $summary['with_coupon'] ?? 0 }}</h5>
                <small class="text-muted">{{ number_format($couponUsageRate ?? 0,1) }}% {{ __('admin.of_orders') }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.without_coupon') }}</div>
                <h5 class="mb-0 text-gold">{{ $summary['without_coupon'] ?? 0 }}</h5>
                <small class="text-muted">{{ number_format(100 - ($couponUsageRate ?? 0),1) }}% {{ __('admin.of_orders') }}</small>
            </div>
        </div>
    </div>

    <!-- KPI CARDS ROW 3: Platform Split -->
    <div class="row g-3 mb-3">
        @php
            $webPlatform = $platformSummary['web'] ?? null;
            $androidPlatform = $platformSummary['android'] ?? null;
            $iosPlatform = $platformSummary['ios'] ?? null;
            $unknownPlatform = $platformSummary['unknown'] ?? null;
        @endphp
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.website_orders') }}</div>
                <h5 class="mb-0 text-gold">{{ $webPlatform->orders ?? 0 }}</h5>
                <small class="text-muted">${{ number_format($webPlatform->revenue ?? 0,2) }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.android_orders') }}</div>
                <h5 class="mb-0 text-gold">{{ $androidPlatform->orders ?? 0 }}</h5>
                <small class="text-muted">${{ number_format($androidPlatform->revenue ?? 0,2) }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.ios_orders') }}</div>
                <h5 class="mb-0 text-gold">{{ $iosPlatform->orders ?? 0 }}</h5>
                <small class="text-muted">${{ number_format($iosPlatform->revenue ?? 0,2) }}</small>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card card-dark p-3 h-100">
                <div class="text-muted small">{{ __('admin.unknown_source') }}</div>
                <h5 class="mb-0 text-gold">{{ $unknownPlatform->orders ?? 0 }}</h5>
                <small class="text-muted">{{ __('admin.old_orders_before_tracking') }}</small>
            </div>
        </div>
    </div>

    <!-- KPI CARDS ROW 4: Order Status Distribution -->
    <div class="row g-3 mb-3">
        <div class="col-12">
            <div class="card card-dark p-3">
                <h6 class="text-gold mb-3"><i class="bi bi-bar-chart me-2"></i>{{ __('admin.orders_by_status') }}</h6>
                <div class="row g-2">
                    @php
                        $statusCounts = [
                            'pending' => 0,
                            'paid' => 0,
                            'shipped' => 0,
                            'cancelled' => 0,
                            'other' => 0
                        ];
                        foreach($statusBreakdown as $sb) {
                            $status = strtolower($sb->status ?? '');
                            if(str_contains($status, 'pending')) $statusCounts['pending'] += $sb->count;
                            elseif(str_contains($status, 'paid') || str_contains($status, 'completed')) $statusCounts['paid'] += $sb->count;
                            elseif(str_contains($status, 'ship')) $statusCounts['shipped'] += $sb->count;
                            elseif(str_contains($status, 'cancel') || str_contains($status, 'refund')) $statusCounts['cancelled'] += $sb->count;
                            else $statusCounts['other'] += $sb->count;
                        }
                    @endphp
                    <div class="col-md-2.4 col-4">
                        <div class="text-center">
                            <h6 class="text-warning mb-1">{{ $statusCounts['pending'] }}</h6>
                            <small class="text-muted">{{ __('admin.pending') }}</small>
                        </div>
                    </div>
                    <div class="col-md-2.4 col-4">
                        <div class="text-center">
                            <h6 class="text-success mb-1">{{ $statusCounts['paid'] }}</h6>
                            <small class="text-muted">{{ __('admin.paid') }}</small>
                        </div>
                    </div>
                    <div class="col-md-2.4 col-4">
                        <div class="text-center">
                            <h6 class="text-info mb-1">{{ $statusCounts['shipped'] }}</h6>
                            <small class="text-muted">{{ __('admin.shipped') }}</small>
                        </div>
                    </div>
                    <div class="col-md-2.4 col-4">
                        <div class="text-center">
                            <h6 class="text-danger mb-1">{{ $statusCounts['cancelled'] }}</h6>
                            <small class="text-muted">{{ __('admin.cancelled') }}</small>
                        </div>
                    </div>
                    <div class="col-md-2.4 col-4">
                        <div class="text-center">
                            <h6 class="text-secondary mb-1">{{ $statusCounts['other'] }}</h6>
                            <small class="text-muted">{{ __('admin.other') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-gold mb-0"><i class="bi bi-bar-chart-line me-2"></i>{{ __('admin.orders_trend') }}</h6>
                    <small class="text-muted">{{ __('admin.daily_breakdown') }}</small>
                </div>
                <canvas id="ordersChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-gold mb-0"><i class="bi bi-cash-stack me-2"></i>{{ __('admin.revenue_trend') }}</h6>
                    <small class="text-muted">{{ __('admin.daily_revenue') }}</small>
                </div>
                <canvas id="revenueChart" height="180"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-gold mb-0"><i class="bi bi-person-check me-2"></i>{{ __('admin.orders_by_account_type') }}</h6>
                    <small class="text-muted">{{ __('admin.registered_vs_guest') }}</small>
                </div>
                <canvas id="accountChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-gold mb-0"><i class="bi bi-pie-chart me-2"></i>{{ __('admin.orders_by_status') }}</h6>
                    <small class="text-muted">{{ __('admin.distribution') }}</small>
                </div>
                <canvas id="statusChart" height="180"></canvas>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-dark p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-gold mb-0"><i class="bi bi-phone me-2"></i>{{ __('admin.orders_by_platform') }}</h6>
                    <small class="text-muted">{{ __('admin.web_vs_app') }}</small>
                </div>
                <canvas id="platformChart" height="180"></canvas>
            </div>
        </div>
    </div>
    @endif


    @php
        $badges = [
            'status' => __('admin.status'),
            'source_platform' => __('admin.platform'),
            'range' => __('admin.range'),
            'discount_filter' => __('admin.discount'),
            'refund_filter' => __('admin.refund'),
            'coupon_code' => __('admin.coupon'),
            'user_search' => __('admin.customer'),
        ];
    @endphp
    <div class="mb-2 d-flex flex-wrap gap-2">
        @foreach($badges as $field => $label)
            @if(request($field))
                <span class="badge bg-secondary">{{ $label }}: {{ request($field) }}</span>
            @endif
        @endforeach
        @if(collect($badges)->keys()->some(fn($f)=>request($f)))
            <a href="{{ route('admin.reports.orders.index') }}" class="badge bg-danger text-decoration-none">{{ __('admin.reset_filters') }}</a>
        @endif
    </div>

    <div class="card card-dark p-3">
        <style>
            /* Collapsible table styles */
            .order-row {
                transition: all 0.3s ease;
                border-left: 3px solid transparent;
            }
            .order-row:hover {
                background-color: rgba(199, 149, 75, 0.15) !important;
            }
            .order-row.table-active {
                background-color: rgba(199, 149, 75, 0.25) !important;
                border-left-color: #c7954b !important;
            }

            /* Status-based row colors (subtle tinting) */
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
            .order-row.status-completed {
                background-color: rgba(16, 185, 129, 0.08);
            }
            .order-row.status-completed:hover {
                background-color: rgba(16, 185, 129, 0.15) !important;
            }

            /* Expanded details styling */
            .expand-details {
                border: 1px solid rgba(199, 149, 75, 0.3);
            }
            .expand-details .bg-dark {
                background-color: #0d1117 !important;
                border-left: 3px solid #c7954b;
                border-radius: 0.5rem;
            }
            .expand-details h6 {
                color: #c7954b;
                font-weight: 600;
                border-bottom: 1px solid rgba(199, 149, 75, 0.2);
                padding-bottom: 0.75rem;
            }
            .expand-details dt {
                color: #a8a8a8;
                font-weight: 500;
            }
            .expand-details dd {
                color: #e0e0e0;
            }

            /* Enhanced badge styles */
            .badge.bg-success {
                background-color: #22c55e !important;
                color: #fff;
            }
            .badge.bg-warning {
                background-color: #fbbf24 !important;
                color: #000;
                font-weight: 500;
            }
            .badge.bg-info {
                background-color: #3b82f6 !important;
                color: #fff;
            }
            .badge.bg-danger {
                background-color: #ef4444 !important;
                color: #fff;
            }
            .badge.bg-dark {
                background-color: #404040 !important;
                border: 1px solid #c7954b;
            }
            .badge.bg-secondary {
                background-color: #6b7280 !important;
                color: #fff;
            }

            /* Financial details styling */
            .expand-details .text-success {
                color: #22c55e !important;
                font-weight: 600;
            }
            .expand-details .text-danger {
                color: #ef4444 !important;
                font-weight: 600;
            }
            .expand-details .text-warning {
                color: #fbbf24 !important;
                font-weight: 600;
            }

            /* Chevron animation */
            .toggle-icon {
                transition: transform 0.3s ease;
                color: #c7954b;
            }
        </style>
        <div class="table-responsive">
            <table class="table table-sm table-dark align-middle" id="ordersTable">
                <thead>
                    <tr class="table-dark">
                        <th style="width: 35px;"></th>
                        <th style="width: 80px;">{{ __('admin.id') }}
                            @if(!empty($facets['ids']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="{{ __('admin.filter_id') }}"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="order_id" data-value="">{{ __('admin.all') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['ids'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="order_id" data-value="{{ $item->value }}">{{ $item->value }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>{{ __('admin.customer') }}
                            @if(!empty($facets['user']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="{{ __('admin.filter_customer') }}"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="user_search" data-value="">{{ __('admin.all') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['user'] as $item)
                                        @php $label = ($item->name ?? '—'); @endphp
                                        <a href="#" class="dropdown-item facet-option" data-field="user_search" data-value="{{ $item->name }}">{{ $label }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>{{ __('admin.status') }}
                            @if(!empty($facets['status']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="{{ __('admin.filter_status') }}"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="status" data-value="">{{ __('admin.all') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['status'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="status" data-value="{{ $item->value }}">{{ $item->value ?? '—' }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>{{ __('admin.platform') }}
                            @if(!empty($facets['platforms']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="{{ __('admin.filter_platform') }}"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="source_platform" data-value="">{{ __('admin.all') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['platforms'] as $item)
                                        @php
                                            $platformValue = $item->platform ?? 'unknown';
                                            $platformLabel = match($platformValue) {
                                                'web' => __('admin.website'),
                                                'android' => __('admin.android_app'),
                                                'ios' => __('admin.ios_app'),
                                                default => __('admin.unknown'),
                                            };
                                        @endphp
                                        <a href="#" class="dropdown-item facet-option" data-field="source_platform" data-value="{{ $platformValue }}">{{ $platformLabel }} <span class="text-light">({{ $item->orders }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>{{ __('admin.total') }}
                            @if(!empty($facets['totals']))
                            <div class="dropdown d-inline float-end">
                                <button class="btn btn-link btn-sm p-0 ms-1" style="color:#c7954b" type="button" data-bs-toggle="dropdown" aria-label="{{ __('admin.filter_total') }}"><i class="bi bi-funnel"></i></button>
                                <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                    <a href="#" class="dropdown-item facet-option" data-field="min_total" data-value="">{{ __('admin.all') }} <span class="text-light">({{ $facets['total'] ?? 0 }})</span></a>
                                    @foreach($facets['totals'] as $item)
                                        <a href="#" class="dropdown-item facet-option" data-field="min_total" data-value="{{ $item->value }}">{{ number_format($item->value,2) }} <span class="text-light">({{ $item->count }})</span></a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </th>
                        <th>{{ __('admin.created_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $o)
                        @php
                            $statusLower = strtolower($o->status ?? '');
                            $statusColor = 'secondary';
                            $statusClass = 'secondary';
                            
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
                            } elseif(str_contains($statusLower,'complet')) {
                                $statusColor = 'success';
                                $statusClass = 'completed';
                            }
                            
                            $subtotal = $o->total_before_discount ?? ($o->total_price + $o->discount_amount);
                        @endphp
                        <tr class="cursor-pointer order-row" data-order-id="{{ $o->id }}">
                            <td class="expand-toggle" style="text-align: center; cursor: pointer;">
                                <i class="bi bi-chevron-right toggle-icon"></i>
                            </td>
                            <td><strong>#{{ $o->id }}</strong></td>
                            <td>{{ $o->user_name ?? $o->guest_name ?? '—' }}</td>
                            <td><span class="badge bg-{{ $statusColor }}">{{ $o->status }}</span></td>
                            <td><span class="badge bg-dark text-gold">{{ $o->source_platform ?: __('admin.unknown') }}</span></td>
                            <td><strong>${{ number_format($o->total_price ?? 0,2) }}</strong></td>
                            <td>{{ \Carbon\Carbon::parse($o->created_at)->format('Y-m-d H:i') }}</td>
                        </tr>
                        <!-- Expanded Details Row -->
                        <tr class="expand-details d-none" data-order-id="{{ $o->id }}">
                            <td colspan="7">
                                <div class="p-3 bg-dark rounded">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <h6 class="text-gold mb-2"><i class="bi bi-person-fill me-2"></i>{{ __('admin.customer_information') }}</h6>
                                            <dl class="row mb-0 small">
                                                <dt class="col-sm-4">{{ __('admin.user_id') }}:</dt>
                                                <dd class="col-sm-8">{{ $o->user_id ?? '—' }}</dd>
                                                
                                                <dt class="col-sm-4">{{ __('admin.name') }}:</dt>
                                                <dd class="col-sm-8">{{ $o->user_name ?? $o->guest_name ?? '—' }}</dd>
                                                
                                                <dt class="col-sm-4">{{ __('admin.email') }}:</dt>
                                                <dd class="col-sm-8">{{ $o->user_email ?? $o->guest_email ?? '—' }}</dd>
                                                
                                                <dt class="col-sm-4">{{ __('admin.type') }}:</dt>
                                                <dd class="col-sm-8">
                                                    @if($o->user_id)
                                                        <span class="badge bg-success">{{ __('admin.registered') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ __('admin.guest') }}</span>
                                                    @endif
                                                </dd>
                                            </dl>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="text-gold mb-2"><i class="bi bi-cash-coin me-2"></i>{{ __('admin.financial_details') }}</h6>
                                            <dl class="row mb-0 small">
                                                <dt class="col-sm-4">{{ __('admin.subtotal') }}:</dt>
                                                <dd class="col-sm-8">${{ number_format($subtotal,2) }}</dd>
                                                
                                                <dt class="col-sm-4">{{ __('admin.discount') }}:</dt>
                                                <dd class="col-sm-8">
                                                    <span class="text-danger">-${{ number_format($o->discount_amount ?? 0,2) }}</span>
                                                </dd>
                                                
                                                <dt class="col-sm-4">{{ __('admin.net_total') }}:</dt>
                                                <dd class="col-sm-8">
                                                    <strong class="text-success">${{ number_format($o->total_price ?? 0,2) }}</strong>
                                                </dd>
                                                
                                                <dt class="col-sm-4">{{ __('admin.refund') }}:</dt>
                                                <dd class="col-sm-8">
                                                    <span class="text-warning">${{ number_format($o->refund_amount ?? 0,2) }}</span>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-2">
                                        <div class="col-md-12">
                                            <h6 class="text-gold mb-2"><i class="bi bi-bookmark-fill me-2"></i>{{ __('admin.additional_information') }}</h6>
                                            <dl class="row mb-0 small">
                                                <dt class="col-sm-2">{{ __('admin.coupon_code') }}:</dt>
                                                <dd class="col-sm-10">{{ $o->coupon_code ?? '—' }}</dd>
                                                
                                                <dt class="col-sm-2">{{ __('admin.status') }}:</dt>
                                                <dd class="col-sm-10"><span class="badge bg-{{ $statusColor }}">{{ $o->status }}</span></dd>
                                                
                                                <dt class="col-sm-2">{{ __('admin.platform') }}:</dt>
                                                <dd class="col-sm-10"><span class="badge bg-dark text-gold">{{ $o->source_platform ?: __('admin.unknown') }}</span></dd>
                                                
                                                <dt class="col-sm-2">{{ __('admin.created') }}:</dt>
                                                <dd class="col-sm-10">{{ \Carbon\Carbon::parse($o->created_at)->format('Y-m-d H:i:s') }}</dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">{{ __('admin.no_orders_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-2">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Handle range selection changes
function handleRangeChange() {
    const rangeSelect = document.getElementById('rangeSelect');
    const customRange = document.getElementById('customRange');
    const dateFilterForm = document.getElementById('dateFilterForm');
    
    if (rangeSelect.value === 'custom') {
        customRange.classList.remove('d-none');
    } else {
        customRange.classList.add('d-none');
        // Clear from/to inputs when using preset range
        document.getElementById('fromDate').value = '';
        document.getElementById('toDate').value = '';
        // Submit the form immediately for preset ranges
        dateFilterForm.submit();
    }
}

// Toggle custom range visibility on page load
document.addEventListener('DOMContentLoaded', () => {
    const rangeSelect = document.getElementById('rangeSelect');
    const customRange = document.getElementById('customRange');
    if (rangeSelect.value === 'custom') {
        customRange.classList.remove('d-none');
    } else {
        customRange.classList.add('d-none');
    }

    // Collapsible rows functionality
    const orderRows = document.querySelectorAll('.order-row');
    orderRows.forEach(row => {
        const expandToggle = row.querySelector('.expand-toggle');
        const toggleIcon = row.querySelector('.toggle-icon');
        const orderId = row.dataset.orderId;
        const expandDetailsRow = document.querySelector(`.expand-details[data-order-id="${orderId}"]`);

        row.style.cursor = 'pointer';
        row.addEventListener('click', (e) => {
            if (e.target.closest('.dropdown')) return; // Prevent expand on dropdown click
            
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
});

document.addEventListener('DOMContentLoaded', () => {
    const labels = {!! json_encode($chartLabels) !!};
    const ordersCount = {!! json_encode($chartOrdersCount) !!};
    const revenue = {!! json_encode($chartRevenue) !!};
    const paidOrders = {!! json_encode($chartPaidOrders) !!};
    const pendingOrders = {!! json_encode($chartPendingOrders) !!};
    const registeredOrders = {!! json_encode($chartRegisteredOrders) !!};
    const guestOrders = {!! json_encode($chartGuestOrders) !!};
    const webOrders = {!! json_encode($chartWebOrders) !!};
    const androidOrders = {!! json_encode($chartAndroidOrders) !!};
    const iosOrders = {!! json_encode($chartIosOrders) !!};
    const platformBreakdown = {!! json_encode($platformBreakdown) !!};
    const statusBreakdown = {!! json_encode($statusBreakdown) !!};

    // Orders Trend Chart
    const ordersCtx = document.getElementById('ordersChart');
    if (ordersCtx) {
        new Chart(ordersCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: '{{ addslashes(__('admin.total_orders')) }}',
                        data: ordersCount,
                        backgroundColor: 'rgba(199, 149, 75, 0.7)', // gold
                        borderColor: '#c7954b',
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} orders`
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Revenue Trend Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        const ctx = revenueCtx.getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, revenueCtx.height);
        gradient.addColorStop(0, 'rgba(22, 199, 154, 0.4)');
        gradient.addColorStop(1, 'rgba(22, 199, 154, 0.05)');

        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: '{{ addslashes(__('admin.revenue')) }}',
                        data: revenue,
                        borderColor: '#16c79a', // green
                        backgroundColor: gradient,
                        borderWidth: 3,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `Revenue: $${Number(ctx.raw ?? 0).toFixed(2)}`
                        }
                    }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Orders by Account Type Chart (Registered vs Guest)
    const accountCtx = document.getElementById('accountChart');
    if (accountCtx) {
        new Chart(accountCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: '{{ addslashes(__('admin.registered_users')) }}',
                        data: registeredOrders,
                        backgroundColor: 'rgba(91, 141, 239, 0.7)', // blue
                        borderColor: '#5b8def',
                        borderWidth: 1
                    },
                    {
                        label: '{{ addslashes(__('admin.guest_users')) }}',
                        data: guestOrders,
                        backgroundColor: 'rgba(255, 159, 67, 0.7)', // orange
                        borderColor: '#ff9f43',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} orders`
                        }
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Orders by Platform Stacked Chart
    const platformCtx = document.getElementById('platformChart');
    if (platformCtx) {
        new Chart(platformCtx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: '{{ addslashes(__('admin.website')) }}',
                        data: webOrders,
                        backgroundColor: 'rgba(199, 149, 75, 0.75)',
                        borderColor: '#c7954b',
                        borderWidth: 1
                    },
                    {
                        label: '{{ addslashes(__('admin.android')) }}',
                        data: androidOrders,
                        backgroundColor: 'rgba(59, 130, 246, 0.75)',
                        borderColor: '#3b82f6',
                        borderWidth: 1
                    },
                    {
                        label: '{{ addslashes(__('admin.ios')) }}',
                        data: iosOrders,
                        backgroundColor: 'rgba(34, 197, 94, 0.75)',
                        borderColor: '#22c55e',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => `${ctx.dataset.label}: ${ctx.raw} orders`
                        }
                    }
                },
                scales: {
                    x: { stacked: true },
                    y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // Orders by Status Pie Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx && statusBreakdown && statusBreakdown.length > 0) {
        const statusLabels = statusBreakdown.map(s => s.status || '{{ addslashes(__('admin.unknown')) }}');
        const statusData = statusBreakdown.map(s => s.count);
        const statusColors = [
            'rgba(34, 197, 94, 0.8)',   // green for paid/completed
            'rgba(251, 191, 36, 0.8)',  // yellow for pending
            'rgba(239, 68, 68, 0.8)',   // red for cancelled/refunded
            'rgba(59, 130, 246, 0.8)',  // blue
            'rgba(168, 85, 247, 0.8)',  // purple
            'rgba(236, 72, 153, 0.8)',  // pink
            'rgba(20, 184, 166, 0.8)',  // teal
        ];

        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusLabels,
                datasets: [{
                    data: statusData,
                    backgroundColor: statusColors.slice(0, statusData.length),
                    borderColor: '#1f2937',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: (ctx) => {
                                const label = ctx.label || '';
                                const value = ctx.raw || 0;
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const percent = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} orders (${percent}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // Facet dropdown: click to set filter and submit
    const facetOptions = document.querySelectorAll('.facet-option');
    const form = document.getElementById('orderFilterForm');
    facetOptions.forEach(opt => {
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
});
</script>
@endpush

