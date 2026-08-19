@extends('layouts.admin')
@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card card-dark p-4">
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

                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-person-gear"></i> {{ __('admin.edit_admin') }}
                    </h5>
                    <a href="{{ route('admin.admin-users.index') }}" class="btn btn-sm btn-outline-dark">{{ __('admin.back') }}</a>
                </div>
                <form method="POST" action="{{ route('admin.admin-users.update', $admin->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-outline mb-3">
                        <label class="form-label small text-muted">{{ __('admin.email') }}</label>
                        <input type="email" name="email" value="{{ $admin->email }}" class="form-control" placeholder="{{ __('admin.email') }}" required />
                    </div>

                    <div class="form-outline mb-3">
                        <label class="form-label small text-muted">{{ __('admin.username') }}</label>
                        <input type="text" name="name" value="{{ $admin->name }}" class="form-control" placeholder="{{ __('admin.placeholder_username') }}" required />
                    </div>

                    <div class="form-outline mb-4">
                        <label class="form-label small text-muted">{{ __('admin.new_password_leave_blank') }}</label>
                        <input type="password" name="password" class="form-control" placeholder="{{ __('admin.placeholder_new_password') }}" />
                    </div>

                    <button type="submit" class="btn btn-gold w-100 fw-semibold">{{ __('admin.update_admin') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
