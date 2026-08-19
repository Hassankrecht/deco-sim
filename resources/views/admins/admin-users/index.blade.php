@extends('layouts.admin')

@section('content')
    @push('head')
        <style>
            .status-pill {
                padding: 6px 12px;
                border-radius: 999px;
                border: 1px solid #1f2937;
                background: #f8f9fb;
                color: #1f2937;
                font-weight: 600;
                font-size: 0.9rem;
            }
            .status-pill.active {
                background: linear-gradient(90deg, #c7954b, #d8aa65);
                color: #0f172a;
                border-color: #1f2937;
            }
        </style>
    @endpush

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 text-gold"><i class="bi bi-shield-lock me-2"></i>{{ __('admin.admins') }}</h5>
                <p class="text-muted small mb-0">{{ __('admin.manage_admin_accounts') }}</p>
            </div>
            <a href="{{ route('admin.admin-users.create') }}" class="btn btn-gold fw-semibold">
                <i class="bi bi-plus-lg me-1"></i> {{ __('admin.create_admin') }}
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success small">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger small mb-3">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-dark p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                    <tr>
                        <th>{{ __('admin.hash') }}</th>
                        <th>{{ __('admin.name') }}</th>
                        <th>{{ __('admin.email') }}</th>
                        <th>{{ __('admin.created') }}</th>
                        <th class="text-end">{{ __('admin.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($admins as $admin)
                        <tr>
                            <td>{{ $admin->id }}</td>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td class="text-muted small">{{ optional($admin->created_at)->format('Y-m-d') }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.admin-users.edit', $admin->id) }}" class="btn btn-sm btn-outline-gold fw-semibold px-3">{{ __('admin.edit') }}</a>
                                <form action="{{ route('admin.admin-users.destroy', $admin->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('{{ __('admin.delete_admin_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger fw-semibold px-3">{{ __('admin.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">{{ __('admin.no_admin_accounts_found') }}</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $admins->links() }}
            </div>
        </div>
    </div>
@endsection
