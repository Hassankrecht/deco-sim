@extends('layouts.app')

@section('title', __('messages.auth.register_title'))
@section('meta_description', 'Register for an account with Deco Sim to enjoy luxury carpentry, interior design, and personalized services.')

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_15_05 PM.png') }}" alt="Register"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.auth.create_account') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item text-light active">Register</li>
            </ol>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container akg-newcard" style="max-width: 720px;">
            <div class="akg-card p-4">
                <h5 class="akg-section-label">{{ __('messages.auth.start_journey') }}</h5>
                <h2 class="akg-section-head mb-4">{{ __('messages.auth.create_account') }}</h2>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="row g-3 js-recaptcha">
                    @csrf
                    <input type="hidden" name="g-recaptcha-response">
                    <div class="col-12">
                        <input id="name" type="text" class="form-control akg-input @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name') }}" placeholder="Full name" required autocomplete="name"
                            autofocus>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <input id="email" type="email" class="form-control akg-input @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" placeholder="Email" required autocomplete="email">
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <input id="password" type="password"
                            class="form-control akg-input @error('password') is-invalid @enderror" name="password"
                            placeholder="Password" required autocomplete="new-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <input id="password-confirm" type="password" class="form-control akg-input"
                            name="password_confirmation" placeholder="Confirm password" required autocomplete="new-password">
                    </div>

                    <div class="col-12 mt-2">
                        <button class="btn btn-gold w-100 py-3 fw-semibold"
                                type="submit"
                                data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"
                                data-size="invisible"
                                data-badge="bottomright">
                            {{ __('messages.auth.btn_register') }}
                        </button>
                    </div>
                    <div class="col-12 text-center text-muted small">
                        {{ __('messages.auth.has_account') }} <a href="{{ route('login') }}" class="text-gold">{{ __('messages.auth.btn_login') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
