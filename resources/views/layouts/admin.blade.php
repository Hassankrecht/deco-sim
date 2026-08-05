{{-- resources/views/layouts/admin.blade.php --}}
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin | Deco Sim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f6f8fb;
            color: #1f2d3d;
        }

        .sidebar {
            width: 240px;
            background: #1a1a1a;
            position: fixed;
            top: 0;
            bottom: 0;
            padding: 20px 0;
            border-right: 1px solid #292929;
        }

        .sidebar a {
            display: block;
            padding: 10px 20px;
            color: #e5e7eb;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background: #2b1e0f;
            color: #fff;
        }

        .content {
            margin-left: 240px;
            padding: 25px 25px 60px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: #1a1a1a;
            border: 1px solid #292929;
            border-radius: 12px;
            padding: 12px 16px;
            color: #e5e7eb;
        }

        .topbar h4 {
            color: #f9fafb;
        }

        .topbar .text-muted {
            color: #cbd5e1 !important;
        }

        .card-dark {
            background: #ffffff;
            border: 1px solid #e6e6e6;
            border-radius: 14px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        }

        .card {
            border: 1px solid #e6e6e6;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        }

        .table-dark {
            --bs-table-bg: #0f172a;
            --bs-table-striped-bg: #111827;
        }

        .table-dark th,
        .table-dark td {
            color: #e5e7eb;
        }

        .form-control,
        .form-select {
            background: #fdfdfd;
            border: 1px solid #d1d5db;
            color: #1f2937;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #c7954b;
            box-shadow: 0 0 0 0.2rem rgba(199, 149, 75, 0.25);
        }

        .btn-gold {
            background: linear-gradient(90deg, #c7954b, #d8aa65);
            border: 1px solid #c7954b;
            color: #0f172a;
        }

        .btn-gold:hover {
            filter: brightness(0.95);
            color: #0f172a;
        }

        .btn-outline-gold {
            border: 1px solid #c7954b;
            color: #c7954b;
            background: transparent;
        }

        .btn-outline-gold:hover {
            background: #c7954b;
            color: #0f172a;
        }

        .btn-outline-dark {
            border-color: #1f2937;
            color: #1f2937;
        }

        .btn-outline-dark:hover {
            background: #1f2937;
            color: #fff;
        }

        .nav-link {
            color: #cbd5e1;
        }

        .nav-link.active {
            color: #fff !important;
        }
    </style>
    @stack('head')
    <style>
        /* تقليص أسهم/روابط الترقيم في جداول الأدمن */
        .pagination .page-link {
            padding: 0.4rem 0.65rem;
            font-size: 0.9rem;
            line-height: 1.2;
        }
        /* إخفاء أيقونات الأسهم الكبيرة (SVG أو الرموز الخاصة « ») */
        nav[role="navigation"] svg,
        nav[role="navigation"] .page-link span[aria-hidden="true"] {
            display: none !important;
        }
        /* إخفاء عناصر السابق/التالي تماماً (يغطي Bootstrap وTailwind) */
        nav[role="navigation"] .pagination .page-item:first-child,
        nav[role="navigation"] .pagination .page-item:last-child,
        nav[role="navigation"] > div > span:first-child,
        nav[role="navigation"] > div > a:first-child,
        nav[role="navigation"] > div > span:last-child,
        nav[role="navigation"] > div > a:last-child {
            display: none !important;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="px-4 mb-4">
            <img src="{{ asset('assets/img/deco-sim-logo.jpg') }}" style="height:46px"
                alt="Deco Sim">
            <div class="small text-muted mt-2">Deco Sim</div>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i
                class="bi bi-speedometer2 me-2"></i>Dashboard</a>
        <a href="{{ route('admin.income.index') }}"
            class="{{ request()->routeIs('admin.income.*') ? 'active' : '' }}"><i
                class="bi bi-cash-coin me-2"></i>Income</a>
        <a href="{{ route('admin.reports.orders.index') }}"
            class="{{ request()->routeIs('admin.reports.orders.*') ? 'active' : '' }}"><i
                class="bi bi-table me-2"></i>Orders report</a>
        <a href="{{ route('admin.orders.index') }}"
            class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}"><i
                class="bi bi-receipt me-2"></i>Orders</a>
        <a href="{{ route('admin.projects.index') }}"
            class="{{ request()->routeIs('admin.projects.*') ? 'active' : '' }}"><i
                class="bi bi-building me-2"></i>Projects</a>
        <a href="{{ route('admin.coupons.index') }}"
            class="{{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"><i
                class="bi bi-ticket-perforated me-2"></i>Coupons</a>
        <a href="{{ route('admin.products.index') }}"
            class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}"><i
                class="bi bi-box-seam me-2"></i>Products</a>
        <a href="{{ route('admin.reviews.index') }}"
            class="{{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}"><i
                class="bi bi-chat-quote me-2"></i>Testimonials</a>
        <a href="{{ route('admin.home.settings.edit') }}"
            class="{{ request()->routeIs('admin.home.settings.*') ? 'active' : '' }}"><i
                class="bi bi-house-door me-2"></i>Website Home</a>
        <a href="{{ route('admin.app-home-settings.edit') }}"
            class="{{ request()->routeIs('admin.app-home-settings.*') ? 'active' : '' }}"><i
                class="bi bi-phone me-2"></i>App Home</a>
        <a href="{{ route('admin.messages.index') }}"
            class="{{ request()->routeIs('admin.messages.*') ? 'active' : '' }}"><i
                class="bi bi-envelope me-2"></i>Messages</a>
        <a href="{{ route('admin.admin-users.index') }}"
            class="{{ request()->routeIs('admin.admin-users.*') ? 'active' : '' }}"><i
                class="bi bi-people me-2"></i>Admins</a>
        <a href="{{ route('admin.users.index') }}"
            class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}"><i
                class="bi bi-person-lines-fill me-2"></i>Users</a>
        <form action="{{ route('admin.logout') }}" method="POST" class="mt-4 px-3">
            @csrf
            <button class="btn btn-sm btn-outline-dark w-100">Logout</button>
        </form>
    </div>

    <div class="content">
        <div class="topbar">
            <h4 class="mb-0">Admin Dashboard</h4>
            <div class="small text-muted">Logged in as {{ auth('admin')->user()->name ?? 'Admin' }}</div>
        </div>

        @yield('content')
    </div>

    @stack('modals')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
