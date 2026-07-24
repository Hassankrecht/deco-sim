@extends('layouts.app')

@section('title', '404 - Page Not Found')
@section('meta_description', 'Sorry, the page you are looking for could not be found. Return to Deco Sim home or browse our services.')

@section('content')
<div class="error-container min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="error-content p-5">
                    <h1 class="display-1 fw-bold text-danger mb-3">404</h1>
                    <h2 class="fs-3 mb-3">Page Not Found</h2>
                    <p class="fs-5 text-muted mb-4">
                        Sorry, the page you are looking for doesn't exist or has been moved.
                    </p>
                    
                    <div class="error-actions">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-shopping-bag"></i> Browse Products
                        </a>
                    </div>

                    <div class="mt-5 pt-5 border-top">
                        <p class="text-muted">
                            <small>
                                Need help? <a href="{{ route('contact') }}">Contact us</a> or 
                                <a href="javascript:history.back()">go back</a>
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
