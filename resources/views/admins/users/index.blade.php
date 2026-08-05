@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h5 class="mb-1 text-gold"><i class="bi bi-person-lines-fill me-2"></i>Users</h5>
                <p class="text-muted small mb-0">Registered website users and how they signed in.</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3 col-6">
                <div class="card card-dark p-3">
                    <div class="text-muted small">Total users</div>
                    <div class="fs-4 fw-bold text-gold">{{ $stats['total'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-dark p-3">
                    <div class="text-muted small">Verified email</div>
                    <div class="fs-4 fw-bold text-gold">{{ $stats['verified'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-dark p-3">
                    <div class="text-muted small">Google login</div>
                    <div class="fs-4 fw-bold text-gold">{{ $stats['google'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card card-dark p-3">
                    <div class="text-muted small">Facebook login</div>
                    <div class="fs-4 fw-bold text-gold">{{ $stats['facebook'] ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="card card-dark p-3 mb-4">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label class="form-label small text-muted">Search</label>
                    <input type="text" name="search" value="{{ $search }}" class="form-control"
                        placeholder="Name, email, or phone">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Login method</label>
                    <select name="provider" class="form-select">
                        <option value="">All</option>
                        <option value="email" {{ $provider === 'email' ? 'selected' : '' }}>Email / Password</option>
                        <option value="google" {{ $provider === 'google' ? 'selected' : '' }}>Google</option>
                        <option value="facebook" {{ $provider === 'facebook' ? 'selected' : '' }}>Facebook</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-gold flex-grow-1">Filter</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-dark">Reset</a>
                </div>
            </form>
        </div>

        <div class="card card-dark p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Phone</th>
                            <th>Login method</th>
                            <th>Email status</th>
                            <th>Orders</th>
                            <th>Total spent</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $loginMethod = $user->auth_provider ?: 'email';
                                $loginLabel = $loginMethod === 'email' ? 'Email / Password' : ucfirst($loginMethod);
                            @endphp
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <div class="small text-muted">{{ $user->email }}</div>
                                </td>
                                <td>{{ $user->phone_number ?: '-' }}</td>
                                <td>
                                    <span class="badge bg-dark text-gold">{{ $loginLabel }}</span>
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success">Verified</span>
                                    @else
                                        <span class="badge bg-secondary">Not verified</span>
                                    @endif
                                </td>
                                <td>{{ $user->checkouts_count ?? 0 }}</td>
                                <td>{{ \App\Support\Currency::format($user->paid_total ?? 0) }}</td>
                                <td class="text-muted small">{{ optional($user->created_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
@endsection
