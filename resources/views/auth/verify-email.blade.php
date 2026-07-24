@extends('layouts.app')

@section('title', 'Verify Email Address')
@section('meta_description', 'Verify your email address to activate your Deco Sim account and access all features securely.')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-envelope"></i> Verify Your Email Address
                    </h4>
                </div>

                <div class="card-body p-4">
                    @if (session('resent'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> A fresh verification link has been sent to your email address.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="verification-content">
                        <div class="text-center mb-4">
                            <i class="fas fa-inbox" style="font-size: 3rem; color: #007bff;"></i>
                        </div>

                        <p class="text-muted text-center mb-4">
                            Before proceeding, please check your email for a verification link.
                            If you did not receive the email, we will gladly send you another.
                        </p>

                        <div class="verification-steps bg-light p-3 rounded mb-4">
                            <h6 class="mb-3"><strong>What's next?</strong></h6>
                            <ol class="ps-3 mb-0">
                                <li>Check your email (including spam folder)</li>
                                <li>Click the verification link</li>
                                <li>Return and enjoy shopping!</li>
                            </ol>
                        </div>

                        <div class="d-grid gap-2">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-redo"></i> Resend Verification Email
                                </button>
                            </form>
                        </div>

                        <div class="mt-3 text-center">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light text-muted text-center">
                    <small>
                        Didn't receive the email? Check your spam/promotions folder, or 
                        <a href="{{ route('contact') }}" class="link-primary">contact us</a>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .verification-content {
        min-height: 400px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .verification-steps {
        border-left: 4px solid #007bff;
    }

    @media (max-width: 768px) {
        .verification-content {
            min-height: auto;
        }
    }
</style>
@endsection
