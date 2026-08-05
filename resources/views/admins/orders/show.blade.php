@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="mb-0 text-gold d-flex align-items-center gap-2">
            <i class="bi bi-receipt"></i> Order #{{ $order->id }}
            @php
                $statusLower = strtolower($order->status ?? '');
                $statusColor = 'secondary';
                if(str_contains($statusLower,'paid')) $statusColor = 'success';
                elseif(str_contains($statusLower,'pending')) $statusColor = 'warning';
                elseif(str_contains($statusLower,'ship')) $statusColor = 'info';
                elseif(str_contains($statusLower,'cancel')) $statusColor = 'danger';
            @endphp
            <span class="badge bg-{{ $statusColor }}">{{ $order->status }}</span>
        </h5>
        <div class="small text-muted">Created {{ $order->created_at->format('Y-m-d H:i') }}</div>
        @if(!empty($order->paid_at))
            <div class="small text-success">Paid at {{ \Carbon\Carbon::parse($order->paid_at)->format('Y-m-d H:i') }}</div>
        @endif
    </div>
    <div class="dropdown">
        <button class="btn btn-outline-gold btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Actions
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="{{ route('admin.orders.index') }}"><i class="bi bi-arrow-left me-2"></i>Back to list</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.orders.show', $order) }}"><i class="bi bi-printer me-2"></i>Print/Preview</a></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Delete this order?');">
                    @csrf @method('DELETE')
                    <button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-2"></i>Delete</button>
                </form>
            </li>
        </ul>
    </div>
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

<div class="row g-3">
    <div class="col-lg-5">
        <div class="card card-dark p-3 mb-3">
            <h6 class="fw-bold mb-2">Status</h6>
            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="d-flex gap-2 align-items-center">
                @csrf @method('PUT')
                <select name="status" class="form-select form-select-sm w-auto">
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ $status }}</option>
                    @endforeach
                </select>
                <button class="btn btn-gold btn-sm">Update</button>
            </form>
        </div>

        <div class="card card-dark p-3">
            <h6 class="fw-bold mb-2">Customer</h6>
            <div class="small text-muted">Name</div>
            <div class="fw-semibold mb-2">{{ $order->name }}</div>
            <div class="small text-muted">Email</div>
            <div class="fw-semibold mb-2">{{ $order->email }}</div>
            <div class="small text-muted">Phone</div>
            <div class="fw-semibold mb-2">{{ $order->phone_number }}</div>
            <div class="small text-muted">Address</div>
            <div class="fw-semibold">{{ $order->address }}, {{ $order->town }}, {{ $order->country }} ({{ $order->zipcode }})</div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card card-dark p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Totals</h6>
            </div>
            @php
                $subtotal = $order->total_before_discount ?? $order->total_price + ($order->discount_amount ?? 0);
            @endphp
            <div class="d-flex justify-content-between small text-muted mt-2">
                <span>Subtotal</span>
                <span>{{ \App\Support\Currency::format($subtotal) }}</span>
            </div>
            <div class="d-flex justify-content-between small text-muted">
                <span>Discount</span>
                <span>{{ \App\Support\Currency::format($order->discount_amount ?? 0) }} {{ $order->coupon?->code ? "({$order->coupon->code})" : '' }}</span>
            </div>
            <div class="d-flex justify-content-between fw-bold fs-6">
                <span>Net total</span>
                <span>{{ \App\Support\Currency::format($order->total_price) }}</span>
            </div>
        </div>

        <div class="card card-dark p-3 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="fw-bold mb-0">Refund</h6>
                @if($order->refund_amount)
                    <span class="badge bg-success">Recorded</span>
                @endif
            </div>
            <div class="small text-muted mb-2">
                Refund allowed فقط بعد الدفع (paid_at) والحالة Paid ثم Cancelled.
                @if(isset($order->paid_at) && $order->paid_at)
                    <br><span class="text-success">Paid at: {{ \Carbon\Carbon::parse($order->paid_at)->format('Y-m-d H:i') }}</span>
                @else
                    <br><span class="text-danger">No paid_at recorded.</span>
                @endif
            </div>
            <div class="d-flex justify-content-between small text-muted">
                <span>Recorded amount</span>
                <span>{{ \App\Support\Currency::format($order->refund_amount ?? 0) }}</span>
            </div>
            @if(in_array($order->status, ['Paid','Cancelled']) && !empty($order->paid_at))
                <form action="{{ route('admin.orders.refund', $order) }}" method="POST" class="mt-3 d-flex gap-2 align-items-end flex-wrap">
                    @csrf
                    <div>
                        <label class="form-label small mb-1">Refund amount</label>
                        <input type="number" name="refund_amount" step="0.01" class="form-control form-control-sm" value="{{ old('refund_amount', $order->refund_amount ?? $order->total_price) }}">
                    </div>
                    <button class="btn btn-outline-danger btn-sm mt-3"><i class="bi bi-arrow-counterclockwise me-1"></i>Record refund</button>
                </form>
            @else
                <div class="alert alert-warning mt-3 mb-0 py-2 small">Refund not allowed unless status is Paid/Cancelled and payment recorded.</div>
            @endif
        </div>

        <div class="card card-dark p-3">
            <h6 class="fw-bold mb-2">Items</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ \App\Support\Currency::format($item->price) }}</td>
                                <td>{{ \App\Support\Currency::format($item->total_price) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
