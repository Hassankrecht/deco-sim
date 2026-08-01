@extends('layouts.app')

@section('title', __('messages.orders.title'))
@section('meta_description', __('messages.orders.meta_description'))

@section('content')
    <div class="container py-5" style="margin-top: 82px">

        <div class="mb-3 d-flex gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-gold">{{ __('messages.dashboard.my_coupons') }}</a>
            <a href="{{ route('dashboard.orders') }}" class="btn btn-gold text-dark">{{ __('messages.dashboard.my_orders') }}</a>
            <a href="{{ route('dashboard.profile') }}" class="btn btn-outline-gold">{{ __('messages.dashboard.profile') }}</a>
        </div>

        <div class="akg-card p-4 mb-4 user-card">
            <h4 class="text-gold mb-2">{{ __('messages.orders.heading') }}</h4>
            <p class="text-muted mb-0">{{ __('messages.orders.description') }}</p>
        </div>

        <div class="akg-card p-4 user-card">
            @if ($orders->isEmpty())
                <p class="text-muted mb-0">{{ __('messages.orders.no_orders') }}</p>
            @else
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.orders.date') }}</th>
                                <th>{{ __('messages.orders.status') }}</th>
                                <th>{{ __('messages.orders.total') }}</th>
                                <th>{{ __('messages.orders.discount') }}</th>
                                <th>{{ __('messages.orders.coupon') }}</th>
                                <th class="text-end">{{ __('messages.orders.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->created_at?->format('Y-m-d') }}</td>
                                    <td><span class="badge bg-secondary">{{ $order->status ?? __('messages.orders.pending') }}</span></td>
                                    <td class="fw-bold text-gold">{{ \App\Support\Currency::format($order->total_price ?? 0) }}</td>
                                    <td class="text-success">- {{ \App\Support\Currency::format($order->discount_amount ?? 0) }}
                                    </td>
                                    <td>{{ $order->coupon?->code ?? '—' }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-gold" data-bs-toggle="modal"
                                            data-bs-target="#orderModal{{ $order->id }}">{{ __('messages.orders.view') }}</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    </div>


@foreach ($orders as $order)
    <div class="modal fade" id="orderModal{{ $order->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content card-dark">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('messages.orders.order_number', ['id' => $order->id]) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <span class="badge bg-secondary">{{ $order->status ?? __('messages.orders.pending') }}</span>
                        <span class="ms-2 text-muted small">{{ $order->created_at?->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table table-dark table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.orders.item') }}</th>
                                    <th class="text-center">{{ __('messages.orders.qty') }}</th>
                                    <th class="text-end">{{ __('messages.orders.price') }}</th>
                                    <th class="text-end">{{ __('messages.orders.total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td class="text-center">{{ $item->quantity }}</td>
                                        <td class="text-end">{{ \App\Support\Currency::format($item->price ?? 0) }}</td>
                                        <td class="text-end">
                                            {{ \App\Support\Currency::format(($item->price ?? 0) * ($item->quantity ?? 0)) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div class="text-muted small">
                            {{ __('messages.orders.coupon') }}: {{ $order->coupon?->code ?? '—' }}<br>
                            {{ __('messages.orders.discount') }}: {{ \App\Support\Currency::format($order->discount_amount ?? 0) }}
                        </div>
                        <div class="text-end">
                            <div class="text-muted">{{ __('messages.orders.subtotal') }}:
                                {{ \App\Support\Currency::format($order->total_before_discount ?? ($order->total_price ?? 0)) }}
                            </div>
                            <div class="fw-bold text-gold fs-5">{{ __('messages.orders.total') }}:
                                {{ \App\Support\Currency::format($order->total_price ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.orders.close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection
