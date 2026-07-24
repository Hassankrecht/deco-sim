<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | Deco Sim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0f0f0f; }
        .login-box {
            max-width: 420px;
            margin: 80px auto;
            background: #1c1c1c;
            border: 1px solid #3a2b17;
            border-radius: 14px;
            padding: 30px 35px;
            color: #fff;
        }
        .btn-gold {
            background: linear-gradient(135deg,#d4af37,#ffcc00);
            border: none;
            color: #000;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="login-box text-center">
    <img src="{{ asset('assets/img/ChatGPT Image Nov 3, 2025, 08_00_27 AM.png') }}" alt="Logo" style="height:60px" class="mb-3">
    <h4 class="mb-3">Admin Panel</h4>
    <p class="text-muted small mb-4">Sign in to manage products, projects, orders.</p>

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login.submit') }}">
        @csrf
        <div class="mb-3 text-start">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control bg-dark text-white border-0" required autofocus>
        </div>
        <div class="mb-3 text-start">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control bg-dark text-white border-0" required>
        </div>
        <div class="mb-3 text-start form-check">
            <input type="checkbox" name="remember" class="form-check-input" id="remember">
            <label for="remember" class="form-check-label small">Remember me</label>
        </div>
        <button class="btn btn-gold w-100 py-2">Login</button>
    </form>
</div>
</body>
</html>
