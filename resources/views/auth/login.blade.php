@extends('layouts.app')

@section('title', __('messages.auth.login_title'))
@section('meta_description', 'Login to your Deco Sim account to access your dashboard, manage orders, and update your profile securely.')

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_15_05 PM.png') }}" alt="Login" class="akg-hero-img"
            loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.auth.login_title') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-light active">Login</li>
            </ol>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container akg-newcard" style="max-width: 720px;">
            <div class="akg-card p-4">
                <h5 class="akg-section-label">{{ __('messages.auth.welcome_back') }}</h5>
                <h2 class="akg-section-head mb-4">{{ __('messages.auth.sign_in') }}</h2>

                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="row g-3">
                    @csrf
                    <div class="col-12">
                        <input id="email" type="email" class="form-control akg-input @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email"
                            autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <input id="password" type="password"
                            class="form-control akg-input @error('password') is-invalid @enderror" name="password"
                            placeholder="Password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-12 d-flex justify-content-between align-items-center">
                        <div class="form-check text-muted">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="remember">
                                {{ __('messages.auth.remember') }}
                            </label>
                        </div>
                        @if (Route::has('password.request'))
                            <a class="small text-gold" href="{{ route('password.request') }}">
                                {{ __('messages.auth.forgot') }}
                            </a>
                        @endif
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-gold w-100 py-3 fw-semibold" type="submit">{{ __('messages.auth.btn_login') }}</button>
                    </div>
                    <div class="col-12 text-center text-muted small">
                        {{ __('messages.auth.no_account') }} <a href="{{ route('register') }}" class="text-gold">{{ __('messages.auth.btn_register') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
