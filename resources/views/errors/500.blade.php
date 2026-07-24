@extends('layouts.app')

@section('title', '500 - Server Error')
@section('meta_description', 'A server error occurred. Please try again later or contact Deco Sim for assistance.')

@section('content')
<div class="error-container min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="error-content p-5">
                    <h1 class="display-1 fw-bold text-danger mb-3">500</h1>
                    <h2 class="fs-3 mb-3">Internal Server Error</h2>
                    <p class="fs-5 text-muted mb-4">
                        Something went wrong on our end. We're working to fix it. Please try again in a moment.
                    </p>
                    
                    <div class="error-actions">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        <a href="javascript:location.reload()" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-redo"></i> Try Again
                        </a>
                    </div>

                    <div class="mt-5 pt-5 border-top">
                        <p class="text-muted">
                            <small>
                                If the problem persists, please <a href="{{ route('contact') }}">contact us</a>
                            </small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .error-container {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .error-content {
        background: white;
        border-radius: 10px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }
    
    .error-content h1 {
        font-size: 5rem;
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .error-content h1 {
            font-size: 3rem;
        }
    }
</style>
@endsection
