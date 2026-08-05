@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 text-gold"><i class="bi bi-ticket-perforated me-2"></i>Coupons</h5>
        </div>

        {{-- Stats --}}
        <div class="row g-3 mb-3">
            <div class="col-md-3 col-6">
                <div class="card card-dark p-3 text-center">
                    <div class="text-muted small">Total</div>
                    <div class="fs-4 fw-bold text-gold">{{ $total }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-dark p-3 text-center">
                    <div class="text-muted small">Active</div>
                    <div class="fs-4 fw-bold text-gold">{{ $active }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-dark p-3 text-center">
                    <div class="text-muted small">Expired</div>
                    <div class="fs-4 fw-bold text-gold">{{ $expired }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-dark p-3 text-center">
                    <div class="text-muted small">Unique users</div>
                    <div class="fs-4 fw-bold text-gold">{{ $uniqueUsers }}</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            {{-- Create (top) --}}
            <div class="col-12">
                <div class="card card-dark p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold text-gold mb-0">Create coupon</h5>
                        <button class="btn btn-sm btn-outline-gold" type="button" data-bs-toggle="collapse" data-bs-target="#createCouponForm" aria-expanded="true">
                            Toggle form
                        </button>
                    </div>
                    <div class="collapse show" id="createCouponForm">
                    <form method="POST" action="{{ route('admin.coupons.store') }}" class="row g-2">
                        @csrf
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Code (optional)</label>
                            <input type="text" name="code" class="form-control form-control-sm" placeholder="Leave empty to auto-generate">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Type</label>
                            <select name="type" class="form-select form-select-sm" required>
                                <option value="percent">Percent</option>
                                <option value="fixed">Fixed</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Value</label>
                            <input type="number" step="0.01" name="value" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Generated for</label>
                            <select name="generated_for" class="form-select form-select-sm">
                                <option value="manual">manual</option>
                                <option value="welcome_auto">welcome_auto</option>
                                <option value="postpay_auto">postpay_auto</option>
                            </select>
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small">Starts at</label>
                            <input type="datetime-local" name="starts_at" class="form-control form-control-sm">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Expires at</label>
                            <input type="datetime-local" name="expiration_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Expiry days (auto)</label>
                            <input type="number" name="expiry_days" class="form-control form-control-sm" min="1" placeholder="e.g. 7">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Min order total</label>
                            <input type="number" step="0.01" name="min_total" class="form-control form-control-sm" value="0">
                        </div>

                        <div class="col-6 col-md-3">
                            <label class="form-label small">Usage limit (total)</label>
                            <input type="number" name="usage_limit" class="form-control form-control-sm" value="1">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">User limit</label>
                            <input type="number" name="user_usage_limit" class="form-control form-control-sm" value="1">
                        </div>
                        <div class="col-6 col-md-3 d-flex align-items-end">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" name="status" value="1" id="status" checked>
                                <label class="form-check-label small" for="status">Active</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 d-flex align-items-end">
                            <button class="btn btn-gold btn-sm w-100">Create</button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>

            {{-- Filters (below) --}}
            <div class="col-12">
                <div class="card card-dark p-3 h-100">
                    <h5 class="fw-bold text-gold mb-3">Filter</h5>
                    <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-2">
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Code</label>
                            <input type="text" name="code" value="{{ $filters['code'] }}" class="form-control">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">User ID</label>
                            <input type="number" name="user_id" value="{{ $filters['user_id'] }}" class="form-control">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Generated for</label>
                            <select name="generated_for" class="form-select">
                                <option value="">Any</option>
                                <option value="manual" {{ $filters['generated_for']==='manual' ? 'selected' : '' }}>manual</option>
                                <option value="welcome_auto" {{ $filters['generated_for']==='welcome_auto' ? 'selected' : '' }}>welcome_auto</option>
                                <option value="postpay_auto" {{ $filters['generated_for']==='postpay_auto' ? 'selected' : '' }}>postpay_auto</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="templates_only" value="1" id="templatesOnly"
                                    {{ $filters['templates_only'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="templatesOnly">Templates only</label>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Status</label>
                            <select name="status" class="form-select">
                                <option value="">Any</option>
                                <option value="active" {{ $filters['status']==='active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $filters['status']==='inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">Type</label>
                            <select name="type" class="form-select">
                                <option value="">Any</option>
                                <option value="percent" {{ $filters['type']==='percent' ? 'selected' : '' }}>Percent</option>
                                <option value="fixed" {{ $filters['type']==='fixed' ? 'selected' : '' }}>Fixed</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">From</label>
                            <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label small">To</label>
                            <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small">Quick range</label>
                            <select name="range" class="form-select">
                                <option value="">Custom</option>
                                <option value="1d" {{ $filters['range']==='1d' ? 'selected' : '' }}>Last 1 day</option>
                                <option value="week" {{ $filters['range']==='week' ? 'selected' : '' }}>Last week</option>
                                <option value="month" {{ $filters['range']==='month' ? 'selected' : '' }}>Last month</option>
                                <option value="3m" {{ $filters['range']==='3m' ? 'selected' : '' }}>Last 3 months</option>
                                <option value="6m" {{ $filters['range']==='6m' ? 'selected' : '' }}>Last 6 months</option>
                                <option value="1y" {{ $filters['range']==='1y' ? 'selected' : '' }}>Last year</option>
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-gold w-100">Apply</button>
                            <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-dark w-100">Reset</a>
                        </div>
                    </form>
                    <div class="small text-muted mt-3">Results: {{ $facetTotal }}</div>
                </div>
            </div>
        </div>
        </div>

        {{-- Coupons table --}}
        <div class="card card-dark p-3 mt-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="fw-bold text-gold mb-0">Coupons</h5>
                <span class="small text-muted">Page {{ $coupons->currentPage() }} of {{ $coupons->lastPage() }}</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Code</th>
                            <th>
                                <div class="d-flex align-items-center gap-1">
                                    Status
                                    @if(!empty($facetStatus))
                                    <div class="dropdown">
                                        <button class="btn btn-link btn-sm p-0 ms-1 text-warning" type="button" data-bs-toggle="dropdown" aria-label="Filter status"><i class="bi bi-funnel"></i></button>
                                        <div class="dropdown-menu dropdown-menu-dark p-2 small">
                                            <a href="{{ request()->fullUrlWithQuery(array_merge($filters,['status'=>''])) }}" class="dropdown-item">All <span class="text-light">({{ $facetTotal ?? 0 }})</span></a>
                                            @foreach($facetStatus as $item)
                                                @php $label = $item->status ? 'active' : 'inactive'; @endphp
                                                <a href="{{ request()->fullUrlWithQuery(array_merge($filters,['status'=>$label])) }}" class="dropdown-item">{{ ucfirst($label) }} <span class="text-light">({{ $item->count }})</span></a>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </th>
                            <th>
                                <div class="d-flex align-items-center gap-1">
                                    Type
                                    @if(!empty($facetTypes))
                                    <div class="dropdown">
        <button class="btn btn-link btn-sm p-0 ms-1 text-warning" type="button" data-bs-toggle="dropdown" aria-label="Filter type"><i class="bi bi-funnel"></i></button>
                                        <div class="dropdown-menu dropdown-menu-dark p-2 small">
                                            <a href="{{ request()->fullUrlWithQuery(array_merge($filters,['type'=>''])) }}" class="dropdown-item">All <span class="text-light">({{ $facetTotal ?? 0 }})</span></a>
                                            @foreach($facetTypes as $item)
                                                <a href="{{ request()->fullUrlWithQuery(array_merge($filters,['type'=>$item->type])) }}" class="dropdown-item">{{ $item->type ?? '—' }} <span class="text-light">({{ $item->count }})</span></a>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </th>
                            <th>Discount</th>
                            <th>Subtotal</th>
                            <th>
                                <div class="d-flex align-items-center gap-1">
                                    User
                                    @if(!empty($facetUsers))
                                    <div class="dropdown">
                                        <button class="btn btn-link btn-sm p-0 ms-1 text-warning" type="button" data-bs-toggle="dropdown" aria-label="Filter user"><i class="bi bi-funnel"></i></button>
                                        <div class="dropdown-menu dropdown-menu-dark p-2 small" style="max-height:300px;overflow:auto;">
                                            <a href="{{ request()->fullUrlWithQuery(array_merge($filters,['user_id'=>''])) }}" class="dropdown-item">All <span class="text-light">({{ $facetTotal ?? 0 }})</span></a>
                                            @foreach($facetUsers as $item)
                                                @if($item->user_id)
                                                <a href="{{ request()->fullUrlWithQuery(array_merge($filters,['user_id'=>$item->user_id])) }}" class="dropdown-item">#{{ $item->user_id }} <span class="text-light">({{ $item->count }})</span></a>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </th>
                            <th>Usage (total)</th>
                            <th>User limit</th>
                            <th>Starts</th>
                            <th>Expires</th>
                            <th>+Days</th>
                            <th>Gen. for</th>
                            <th width="140">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($coupons as $coupon)
                            @php
                                $facetUse = $usage[$coupon->id] ?? null;
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $coupon->code }}</td>
                                <td>
                                    <span class="badge {{ $coupon->status ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $coupon->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ ucfirst($coupon->type) }}</td>
                                <td>{{ $coupon->type === 'percent' ? $coupon->value . '%' : \App\Support\Currency::format($coupon->value) }}</td>
                                <td class="small">{{ $coupon->min_total ? \App\Support\Currency::format($coupon->min_total) : '—' }}</td>
                                <td>
                                    @if($coupon->user)
                                        #{{ $coupon->user->id }} — {{ $coupon->user->name }}
                                    @else
                                        <span class="text-muted">Template</span>
                                    @endif
                                </td>
                                <td class="small">
                                    @php
                                        $usageLimit = ($coupon->usage_limit && $coupon->usage_limit > 0) ? $coupon->usage_limit : '∞';
                                    @endphp
                                    <div>Used: {{ $coupon->used_count }} / {{ $usageLimit }}</div>
                                    <div>Orders: {{ $facetUse->orders_count ?? 0 }}</div>
                                </td>
                                <td class="small">
                                    Per-user: {{ $coupon->user_usage_limit ?? '∞' }}
                                </td>
                                <td class="small">
                                    {{ $coupon->starts_at ? \Carbon\Carbon::parse($coupon->starts_at)->format('Y-m-d H:i') : '—' }}
                                </td>
                                <td class="small">
                                    {{ $coupon->expiration_date ? \Carbon\Carbon::parse($coupon->expiration_date)->format('Y-m-d H:i') : '—' }}
                                </td>
                                <td class="small">
                                    {{ $coupon->expiry_days ? ($coupon->expiry_days . ' d') : '—' }}
                                </td>
                                <td class="small text-muted">{{ $coupon->generated_for ?? 'manual' }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-dark dropdown-toggle" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
                                                    @csrf
                                                    @method('PUT')
                                                    <input type="hidden" name="status" value="{{ $coupon->status ? 0 : 1 }}">
                                                    <button type="submit" class="dropdown-item">
                                                        {{ $coupon->status ? 'Deactivate' : 'Activate' }}
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#editCouponModal-{{ $coupon->id }}">
                                                    Edit
                                                </button>
                                            </li>
                                            <li>
                                                <button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#usageCouponModal-{{ $coupon->id }}">
                                                    Users
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete this coupon?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">Delete</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="10" class="text-center text-muted py-4">No coupons found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $coupons->links() }}
            </div>
        </div>
    </div>
@endsection

@push('modals')
    @foreach ($coupons as $coupon)
        @php
            $startsVal = $coupon->starts_at ? \Carbon\Carbon::parse($coupon->starts_at)->format('Y-m-d\TH:i') : '';
            $expiresVal = $coupon->expiration_date ? \Carbon\Carbon::parse($coupon->expiration_date)->format('Y-m-d\TH:i') : '';
            $usageRows = $coupon->generated_for === 'manual'
                ? ($usageUsers[$coupon->id] ?? collect())->sortByDesc('last_used_at')
                : collect();
        @endphp

        {{-- Edit Modal --}}
        <div class="modal fade" id="editCouponModal-{{ $coupon->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Coupon #{{ $coupon->id }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.coupons.update', $coupon) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body row g-2">
                            <div class="col-md-4">
                                <label class="form-label small">Type</label>
                                <select name="type" class="form-select" required>
                                    <option value="percent" {{ $coupon->type === 'percent' ? 'selected' : '' }}>Percent</option>
                                    <option value="fixed" {{ $coupon->type === 'fixed' ? 'selected' : '' }}>Fixed</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Value</label>
                                <input type="number" step="0.01" name="value" class="form-control" value="{{ $coupon->value }}" required>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status" value="1" id="statusEdit{{ $coupon->id }}" {{ $coupon->status ? 'checked' : '' }}>
                                    <label class="form-check-label" for="statusEdit{{ $coupon->id }}">Active</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small">Starts at</label>
                                <input type="datetime-local" name="starts_at" class="form-control" value="{{ $startsVal }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">Expires at</label>
                                <input type="datetime-local" name="expiration_date" class="form-control" value="{{ $expiresVal }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Expiry days (auto)</label>
                                <input type="number" name="expiry_days" class="form-control" min="1" value="{{ $coupon->expiry_days }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Usage limit (total)</label>
                                <input type="number" name="usage_limit" class="form-control" value="{{ $coupon->usage_limit }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">User limit</label>
                                <input type="number" name="user_usage_limit" class="form-control" value="{{ $coupon->user_usage_limit }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Min order total</label>
                                <input type="number" step="0.01" name="min_total" class="form-control" value="{{ $coupon->min_total }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">Generated for</label>
                                <select name="generated_for" class="form-select">
                                    <option value="manual" {{ $coupon->generated_for === 'manual' ? 'selected' : '' }}>manual</option>
                                    <option value="welcome_auto" {{ $coupon->generated_for === 'welcome_auto' ? 'selected' : '' }}>welcome_auto</option>
                                    <option value="postpay_auto" {{ $coupon->generated_for === 'postpay_auto' ? 'selected' : '' }}>postpay_auto</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-gold">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Usage Modal --}}
        <div class="modal fade" id="usageCouponModal-{{ $coupon->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Usage — {{ $coupon->code }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if($usageRows->isEmpty())
                            <p class="text-muted mb-0">No checkouts found for this coupon.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th>User ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Uses</th>
                                            <th>Per-user limit</th>
                                            <th>Remaining</th>
                                            <th>Last used at</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($usageRows as $row)
                                            <tr>
                                                <td>{{ $row->user_id }}</td>
                                                <td>{{ $row->user->name ?? '—' }}</td>
                                                <td>{{ $row->user->email ?? '—' }}</td>
                                                <td>{{ $row->uses }}</td>
                                                @php
                                                    $perUserLimit = $coupon->user_usage_limit ?: '∞';
                                                    $remaining = is_numeric($coupon->user_usage_limit)
                                                        ? max(($coupon->user_usage_limit ?? 0) - $row->uses, 0)
                                                        : '∞';
                                                @endphp
                                                <td>{{ $perUserLimit }}</td>
                                                <td>{{ $remaining }}</td>
                                                <td>{{ $row->last_used_at ? \Carbon\Carbon::parse($row->last_used_at)->format('Y-m-d H:i') : '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endpush
