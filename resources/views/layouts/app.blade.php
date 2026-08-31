<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">


        <title>@yield('title', config('app.name', __('messages.meta.site_name')))</title>
        <meta name="description" content="@yield('meta_description', config('app.name', __('messages.meta.site_name')))">
        <meta property="og:title" content="@yield('og_title', View::getSection('title') ?? config('app.name', __('messages.meta.site_name')))">
        <meta property="og:description" content="@yield('og_description', View::getSection('meta_description') ?? config('app.name', __('messages.meta.site_name')))">
        <meta property="og:image" content="@yield('og_image', asset('assets/img/deco-sim-social.jpg'))">
        <meta property="og:type" content="website">

        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}" />

        <!-- Hreflang tags for multi-language SEO -->
        @foreach(config('app.frontend_locales', ['pt','en']) as $locale)
                <link rel="alternate" hreflang="{{ $locale }}" href="{{ url()->current() }}?lang={{ $locale }}" />
        @endforeach

        <!-- JSON-LD Structured Data -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": "{{ __('messages.meta.site_name') }}",
            "url": "{{ url('/') }}",
            "logo": "{{ asset('assets/img/deco-sim-logo.jpg') }}",
            "contactPoint": [{
                "@type": "ContactPoint",
                "telephone": "+244 972 100 585",
                "contactType": "{{ __('messages.meta.contact_type') }}",
                "areaServed": "AO"
            }],
            "sameAs": [
                "https://www.facebook.com/profile.php?id=61593072766971",
                "https://www.instagram.com/decosim1/",
                "https://www.tiktok.com/@decosim685"
            ]
        }
        </script>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon-deco-sim.png') }}" type="image/png">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Icons & Bootstrap -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Consistent reCAPTCHA badge position/size across pages -->

    <!-- Position reCAPTCHA badge consistently (avoid overlap with chat bubble) -->


    <!-- Animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- ===================== THE MAIN MERGED THEME FILE ===================== -->
    <link href="{{ asset('assets/css/AKG-Luxury.css') }}?v=20260130b" rel="stylesheet">
    @if(app()->getLocale() === 'ar')
    <style>
        /* RTL dropdown alignment */
        html[dir="rtl"] .navbar-nav .dropdown-menu { text-align: right; }
        html[dir="rtl"] .navbar-nav .dropdown-menu-end { left: 0 !important; right: auto !important; }
    </style>
    @endif
    @if(app()->getLocale() !== 'ar')
    <style>
        .akg-nav-tight {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
    </style>
    @endif
    {{-- $theme is now provided by the view composer. --}}
    @if ($theme)
        <style>
            :root {
                @if (!empty($theme->theme_primary))
                    --akg-gold: {{ $theme->theme_primary }};
                @endif
                @if (!empty($theme->theme_dark))
                    --akg-dark: {{ $theme->theme_dark }};
                @endif
                @if (!empty($theme->theme_bg))
                    --akg-bg: {{ $theme->theme_bg }};
                @endif
                @if (!empty($theme->theme_text))
                    --akg-text: {{ $theme->theme_text }};
                @endif
            }
            body {
                @if (!empty($theme->theme_bg)) background: {{ $theme->theme_bg }}; @endif
                @if (!empty($theme->body_text_color)) color: {{ $theme->body_text_color }}; @endif
            }
            h1, h2, h3, h4, h5, h6 {
                @if (!empty($theme->headings_color)) color: {{ $theme->headings_color }} !important; @endif
            }
            a {
                @if (!empty($theme->link_color)) color: {{ $theme->link_color }}; @endif
            }
            a:hover {
                @if (!empty($theme->link_color)) color: {{ $theme->link_color }}cc; @endif
            }
            .btn-gold {
                @if (!empty($theme->btn_global_primary_color))
                    background: {{ $theme->btn_global_primary_color }};
                    border-color: {{ $theme->btn_global_primary_color }};
                    color: #0f172a;
                @endif
                @if (!empty($theme->btn_global_primary_style) && $theme->btn_global_primary_style === 'outline')
                    background: transparent;
                    color: {{ $theme->btn_global_primary_color ?? '#c7954b' }};
                @elseif (!empty($theme->btn_global_primary_style) && $theme->btn_global_primary_style === 'pill')
                    border-radius: 999px;
                @endif
            }
            .btn-outline-gold {
                @if (!empty($theme->btn_global_secondary_color))
                    border-color: {{ $theme->btn_global_secondary_color }};
                    color: {{ $theme->btn_global_secondary_color }};
                @endif
                @if (!empty($theme->btn_global_secondary_style) && $theme->btn_global_secondary_style === 'solid')
                    background: {{ $theme->btn_global_secondary_color ?? '#ffffff' }};
                    color: #0f172a;
                @elseif (!empty($theme->btn_global_secondary_style) && $theme->btn_global_secondary_style === 'pill')
                    border-radius: 999px;
                @endif
            }
        </style>
    @endif
</head>

<body>

    @php
        $cartCount = session('cart_count', 0);
        $cartTotal = session('cart_total', 0);
    @endphp

    <!-- ===================== TOP GOLD BAR ===================== -->
    <div class="akg-navbar-topline" style="margin-top: 0;"></div>

    <!-- ===================== NAVBAR ===================== -->
    <nav class="akg-navbar navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">

            <!-- Brand -->
            <a class="navbar-brand d-flex align-items-center flex-shrink-0 me-lg-4" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/deco-sim-logo.jpg') }}" alt="Deco Sim logo"
                    class="akg-logo">
                <span class="akg-brand-text ms-2">{{ config('app.name', 'Deco Sim') }}</span>
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#akgNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu -->
            <div class="collapse navbar-collapse" id="akgNav">
                <ul class="navbar-nav align-items-lg-center gap-lg-1 ms-auto small {{ app()->getLocale() === 'ar' ? 'akg-nav-rtl' : 'akg-nav-tight' }}">       
                    @php
                        $processText = __('messages.nav.process');
                        if ($processText === 'messages.nav.process') {
                            $processText = app()->getLocale() === 'ar' ? 'عملية العمل' : 'Process';
                        }
                        $pricingText = __('messages.nav.pricing');
                        if ($pricingText === 'messages.nav.pricing') {
                            $pricingText = app()->getLocale() === 'ar' ? 'الأسعار' : 'Pricing';
                        }
                        $testimonialsText = __('messages.nav.testimonials');
                        if ($testimonialsText === 'messages.nav.testimonials') {
                            $testimonialsText = app()->getLocale() === 'ar' ? 'آراء العملاء' : 'Testimonials';
                        }
                        $navItems = [
                            ['route' => 'home', 'label' => __('messages.nav.home')],
                            ['route' => 'services', 'label' => __('messages.nav.services')],
                            ['route' => 'projects.index', 'label' => __('messages.nav.projects')],
                            ['route' => 'process', 'label' => $processText],
                            ['route' => 'pricing', 'label' => $pricingText],
                            ['route' => 'about', 'label' => __('messages.nav.about')],
                            ['route' => 'testimonials', 'label' => $testimonialsText],
                            ['route' => 'products.index', 'label' => __('messages.nav.products') ?? 'Shop'],
                            ['route' => 'contact', 'label' => __('messages.nav.contact'), 'extra' => 'data-track-action="nav_contact"'],
                        ];
                        $extraItems = [
                            'cart' => true,
                            'auth' => true,
                            'lang' => true,
                        ];
                        $allItems = [];
                        foreach ($navItems as $item) {
                            $allItems[] = [
                                'type' => 'nav',
                                'data' => $item
                            ];
                        }
                        $allItems[] = ['type' => 'cart'];
                        $allItems[] = ['type' => 'auth'];
                        $allItems[] = ['type' => 'lang'];
                        if (app()->getLocale() === 'ar') {
                            $allItems = array_reverse($allItems);
                        }
                    @endphp
                    @foreach ($allItems as $item)
                        @if ($item['type'] === 'nav')
                            <li class="nav-item">
                                <a class="nav-link px-2" href="{{ route($item['data']['route']) }}" {!! $item['data']['extra'] ?? '' !!}>{{ $item['data']['label'] }}</a>
                            </li>
                        @elseif ($item['type'] === 'cart')
                            <li class="nav-item dropdown mx-lg-1 akg-nav-tight">
                                <a class="nav-link dropdown-toggle text-gold" href="#" data-bs-toggle="dropdown">
                                    <i class="fa fa-shopping-cart me-1"></i>
                                    {{ __('messages.nav.cart') }}
                                    <span class="badge bg-gold cart-count">{{ $cartCount }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end akg-dropdown">
                                    <li class="d-flex justify-content-between">
                                        <span>{{ __('messages.cart.dropdown_items') }}:</span> <span class="fw-bold cart-count">{{ $cartCount }}</span>
                                    </li>
                                    <li class="d-flex justify-content-between mt-2">
                                        <span>{{ __('messages.cart.dropdown_total') }}:</span> <span class="fw-bold cart-total">{{ \App\Support\Currency::format($cartTotal) }}</span>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <a href="{{ route('cart.index') }}" class="btn btn-gold w-100 fw-semibold">
                                            {{ __('messages.cart.view_cart') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @elseif ($item['type'] === 'auth')
                            <li class="nav-item dropdown ms-lg-1 akg-nav-tight">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                                    <i class="fa fa-user-circle me-1"></i>
                                    @auth {{ Str::limit(Auth::user()->name, 10) }} @endauth
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end akg-dropdown">
                                    @guest
                                        <li><a href="{{ route('login') }}" class="dropdown-item">{{ __('messages.nav.login') }}</a></li>
                                        <li><a href="{{ route('register') }}" class="dropdown-item">{{ __('messages.nav.register') }}</a></li>
                                    @else
                                        <li><a href="{{ route('dashboard') }}" class="dropdown-item">{{ __('messages.nav.dashboard') }}</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('logout') }}" method="POST">@csrf
                                                <button class="dropdown-item text-danger">
                                                    <i class="fa fa-power-off"></i> Logout
                                                </button>
                                            </form>
                                        </li>
                                    @endguest
                                </ul>
                            </li>
                        @elseif ($item['type'] === 'lang')
                            <li class="nav-item dropdown ms-lg-1 akg-nav-tight">
                                <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                    {{ strtoupper(app()->getLocale()) }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end akg-dropdown">
                                    @foreach (config('app.frontend_locales') as $locale)
                                        <li>
                                            <a class="dropdown-item {{ app()->getLocale() === $locale ? 'active' : '' }}" href="{{ route('lang.switch', $locale) }}">
                                                {{ strtoupper($locale) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
                                  

    <!-- ===================== CONTENT ===================== -->
    <main class="akg-main">
        @yield('content')
    </main>

    <!-- ===================== FOOTER ===================== -->
    <footer class="akg-footer text-center">
        <div class="container py-4">
            <p class="mb-2">© {{ date('Y') }} Deco Sim. {{ __('messages.footer.all_rights') }}</p>
            <div class="mb-3"><small class="text-muted">Developed by Hassan Krecht</small></div>
            <div class="akg-footer-menu mb-3">
                <a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a> |
                <a href="{{ route('about') }}">{{ __('messages.nav.about') }}</a> |
                <a href="{{ route('services') }}">{{ __('messages.nav.services') }}</a> |
                <a href="{{ route('process') }}">{{ $processText }}</a> |
                <a href="{{ route('products.index') }}">{{ __('messages.nav.products') }}</a> |
                <a href="{{ route('projects.index') }}">{{ __('messages.nav.projects') }}</a> |
                <a href="{{ route('contact') }}">{{ __('messages.nav.contact') }}</a>
            </div>
            <div class="akg-footer-social d-flex justify-content-center gap-3">
                <a class="btn btn-outline-gold btn-sm rounded-circle" href="https://www.facebook.com/profile.php?id=61593072766971" target="_blank"
                    rel="noopener noreferrer" aria-label="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a class="btn btn-outline-gold btn-sm rounded-circle" href="https://www.instagram.com/decosim1/"
                    target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <a class="btn btn-outline-gold btn-sm rounded-circle" href="https://www.tiktok.com/@decosim685" target="_blank"
                    rel="noopener noreferrer" aria-label="TikTok">
                    <i class="fab fa-tiktok"></i>
                </a>
                <a class="btn btn-outline-gold btn-sm rounded-circle" href="https://wa.me/244972100585"
                    target="_blank" rel="noopener" aria-label="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
            </div>
        </div>
    </footer>

    <!-- ===================== SCRIPTS ===================== -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Coupon and userCoupons logic should be moved to the controller and passed as variables. --}}

    {{-- $promoCoupons is now provided by the view composer. --}}

    @php
        $userCouponsCount = isset($userCoupons) ? $userCoupons->count() : 0;
    @endphp

    <style>
        .coupon-fab {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 9999;
            background: linear-gradient(120deg, #ffda7b, #f5b642);
            color: #0f172a;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.28);
            padding: 8px 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            border: 1px dashed #0f172a;
            animation: couponPulse 3s ease-in-out infinite;
        }

        .coupon-fab i {
            color: #0f172a;
        }

        .coupon-fab span {
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        @keyframes couponPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(245, 182, 66, 0.5);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(245, 182, 66, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(245, 182, 66, 0);
            }
        }

        .coupon-panel {
            position: fixed;
            top: 64px;
            left: 0;
            right: 0;
            transform: none;
            width: 100%;
            max-width: none;
            z-index: 9999;
            background: transparent;
            border: 0;
            border-radius: 0;
            box-shadow: none;
            padding: 12px 10px;
            color: #f8fafc;
            display: none;
            font-size: 1rem;
        }

        .coupon-panel::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: rgba(255, 255, 255, 0.12);
            pointer-events: none;
        }

        .coupon-panel.active {
            display: block;
        }

        .coupon-list {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;
            gap: 12px;
            padding: 8px 0;
            margin-top: 4px;
            justify-content: center;
            /* العناصر تبدأ من الوسط */
            flex: 1;
        }

        .coupon-node {
            position: relative;
            min-width: 160px;
            padding: 10px 12px;
            border: 1px solid #c7954b;
            border-radius: 12px;
            background: rgba(15, 23, 42, 0.9);
            color: #e5e7eb;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .coupon-node::before {
            content: '';
            position: absolute;
            top: 10px;
            left: -6px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(120deg, #fbbf24, #f59e0b);
            box-shadow: 0 0 0 3px rgba(251, 191, 36, 0.18);
        }

        .coupon-node:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.28);
            border-color: #fbbf24;
        }

        .coupon-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            background: linear-gradient(120deg, #fbbf24, #f59e0b);
            border: 1px dashed rgba(15, 23, 42, 0.3);
            border-radius: 10px;
            color: #0f172a;
            font-weight: 800;
            font-size: 13px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
            margin-bottom: 4px;
        }

        .coupon-panel small {
            color: #dbeafe;
        }

        .coupon-bullet {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            background: rgba(59, 130, 246, 0.12);
            color: #e0f2fe;
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 999px;
            font-weight: 700;
            margin: 2px;
        }

        .coupon-countdown {
            background: linear-gradient(90deg, #ef4444, #f59e0b);
            color: #0f172a;
            padding: 3px 6px;
            border-radius: 10px;
            font-weight: 800;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
            font-size: 10px;
        }

        .coupon-promobar {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9998;
            background: rgba(17, 24, 39, 0.92);
            border: 1px solid #c7954b;
            border-radius: 12px;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25);
            padding: 10px 12px;
            min-width: 260px;
            max-width: 340px;
            color: #e5e7eb;
        }

        .promo-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .promo-chip {
            border: 1px solid #1f2937;
            border-radius: 10px;
            padding: 8px 10px;
            margin-bottom: 8px;
            background: #0b1220;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        }

        .promo-chip:last-child {
            margin-bottom: 0;
        }

        .promo-meta {
            font-size: 12px;
            color: #cbd5e1;
        }

        .btn-close-mini {
            padding: 4px 8px;
            font-size: 12px;
            border: 1px solid #c7954b;
            border-radius: 8px;
            background: transparent;
            color: #c7954b;
        }

        .btn-copy {
            padding: 4px 8px;
            font-size: 12px;
        }

        .promo-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            padding: 6px 10px;
            font-size: 12px;
            border: 1px solid #c7954b;
            border-radius: 10px;
            background: rgba(17, 24, 39, 0.9);
            color: #f8fafc;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        }

        @media (max-width: 767px) {
            .coupon-fab {
                top: 15px;
                left: 15px;
                padding: 10px 14px;
            }

            .coupon-panel {
                top: 58px;
                left: 0;
                right: 0;
                width: 100%;
                transform: none;
                padding: 10px 10px;
            }
        }
    </style>

    <div class="coupon-fab" id="couponFab">
        <i class="fa fa-bolt"></i>
        <span>Coupons</span>
        @auth
            <span class="badge bg-dark text-light"
                style="font-weight:800; border:1px solid #c7954b;">{{ $userCouponsCount }}</span>
        @endauth
    </div>

    @if ($promoCoupons->isNotEmpty())
        <div class="coupon-promobar" id="couponPromobar">
            <div class="promo-header">
                <span class="text-gold fw-bold">Deals</span>
                <button class="btn-close-mini" id="promoClose">×</button>
            </div>
            @foreach ($promoCoupons as $p)
                <div class="promo-chip">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-gold">{{ $p->code }}</span>
                        <span class="badge bg-dark text-light">{{ $p->generated_for }}</span>
                    </div>
                    <div class="promo-meta">Spend {{ \App\Support\Currency::format($p->min_total ?? 0) }} get
                        {{ $p->type === 'percent' ? $p->value . '% off' : \App\Support\Currency::format($p->value) . ' off' }}</div>
                    @if ($p->expiration_date)
                        <div class="promo-meta">Ends {{ \Carbon\Carbon::parse($p->expiration_date)->format('Y-m-d') }}
                        </div>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mt-1">
                        @if ($p->expiration_date)
                            <small class="promo-meta mb-0" style="font-size:11px;">
                                Ends in
                                {{ \Carbon\Carbon::parse($p->expiration_date)->diffForHumans(null, true, false, 2) }}
                            </small>
                        @else
                            <small class="promo-meta mb-0" style="font-size:11px;">No expiry</small>
                        @endif
                        <button class="btn btn-outline-gold btn-copy" data-code="{{ $p->code }}"
                            style="padding:3px 6px;font-size:11px;margin-left:8px;">Copy</button>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="promo-toggle d-none" id="promoToggle">Deals</button>
    @endif

    <div class="coupon-panel" id="couponPanel">
        @auth
            @if (session('error'))
                <div class="alert alert-danger py-2 px-3 small mb-2">{{ session('error') }}</div>
            @elseif(session('success'))
                <div class="alert alert-success py-2 px-3 small mb-2">{{ session('success') }}</div>
            @endif
            @if ($userCoupons->isNotEmpty())
                <div class="d-flex align-items-center w-100 gap-2">
                    <button class="btn btn-xs btn-outline-light" id="closeCouponPanel"
                        style="padding:4px 8px;font-size:12px;">×</button>
                    <div class="coupon-list flex-grow-1">
                        @foreach ($userCoupons as $c)
                            <div class="coupon-node" data-exp="{{ $c->expiration_date }}">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="coupon-chip mb-0">
                                        <i class="fa fa-ticket-alt"></i>
                                        <span>{{ $c->code }}</span>
                                    </div>
                                    <div class="text-end small">
                                        <div>{{ $c->type === 'percent' ? $c->value . '% OFF' : \App\Support\Currency::format($c->value) . ' OFF' }}</div>
                                        @if ($c->min_total)
                                            <div class="text-muted">Min {{ \App\Support\Currency::format($c->min_total) }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2 small mt-1" style="font-size:12px;">
                                    @if ($c->starts_at)
                                        <span class="badge bg-dark">From
                                            {{ \Carbon\Carbon::parse($c->starts_at)->format('Y-m-d') }}</span>
                                    @endif
                                    @if ($c->expiration_date)
                                        <span class="badge bg-secondary">Ends
                                            {{ \Carbon\Carbon::parse($c->expiration_date)->format('Y-m-d') }}</span>
                                    @endif
                                </div>
                                @if ($c->expiration_date)
                                    <div class="coupon-countdown mt-1" data-countdown="{{ $c->expiration_date }}"></div>
                                @endif
                                <div class="d-flex gap-2 justify-content-end mt-2">
                                    <button class="btn btn-xs btn-outline-gold copy-coupon"
                                        data-code="{{ $c->code }}"
                                        style="padding:4px 8px;font-size:11px;">Copy</button>
                                    <form method="POST" action="{{ route('coupon.apply') }}">
                                        @csrf
                                        <input type="hidden" name="code" value="{{ $c->code }}">
                                        <button class="btn btn-xs btn-outline-gold"
                                            style="padding:4px 8px;font-size:11px;">Apply</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="d-flex justify-content-between align-items-center">
                    <strong class="text-gold">No coupon yet</strong>
                    <button class="btn btn-sm btn-outline-light" id="closeCouponPanel">×</button>
                </div>
                <p class="text-muted mb-0">Grab your welcome coupon from the cart.</p>
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('cart.index') }}" class="btn btn-sm btn-gold text-dark">Cart</a>
                </div>
            @endif
        @else
            <div class="d-flex justify-content-between align-items-center">
                <strong class="text-gold">Exclusive offers waiting</strong>
                <button class="btn btn-sm btn-outline-light" id="closeCouponPanel">×</button>
            </div>
            <div class="mb-2 d-flex flex-wrap">
                <span class="coupon-bullet"><i class="fa fa-gift"></i> Unlock a welcome coupon when you sign in.</span>
                <span class="coupon-bullet"><i class="fa fa-percent"></i> Save 10% on your first order today.</span>
                <span class="coupon-bullet"><i class="fa fa-sparkles"></i> Members get exclusive seasonal deals.</span>
            </div>
            <div class="coupon-countdown mt-1" id="couponCountdownGuest"
                data-guest-exp="{{ now()->addDay()->toIso8601String() }}"></div>
            <p class="mb-0" style="color:#bfdbfe;">Sign in or create an account to claim your coupons.</p>
        @endauth
    </div>

    <script>
        // Navbar scroll effect
        document.addEventListener("scroll", () => {
            const nav = document.querySelector(".akg-navbar");
            if (window.scrollY > 10) nav.classList.add("scrolled");
            else nav.classList.remove("scrolled");
        });

        // Coupon widget toggle
        (function() {
            const fab = document.getElementById('couponFab');
            const panel = document.getElementById('couponPanel');
            const closeBtn = document.getElementById('closeCouponPanel');
            if (!fab || !panel) return;

            fab.addEventListener('click', () => {
                panel.classList.toggle('active');
            });
            closeBtn?.addEventListener('click', () => panel.classList.remove('active'));

            // Copy buttons per coupon
            document.querySelectorAll('.copy-coupon').forEach(btn => {
                btn.addEventListener('click', () => {
                    const code = btn.dataset.code || '';
                    if (!code) return;
                    navigator.clipboard.writeText(code).then(() => {
                        btn.textContent = 'Copied';
                        setTimeout(() => btn.textContent = 'Copy', 1200);
                    });
                });
            });

            // Countdown per coupon
            document.querySelectorAll('[data-countdown]').forEach(el => {
                const end = new Date(el.dataset.countdown);
                const tick = () => {
                    const diff = end - new Date();
                    if (diff <= 0) {
                        el.textContent = 'Expired';
                        return;
                    }
                    const totalHours = Math.floor(diff / 1000 / 60 / 60);
                    const days = Math.floor(totalHours / 24);
                    const hours = totalHours % 24;
                    const m = Math.floor((diff / 1000 / 60) % 60);
                    const s = Math.floor((diff / 1000) % 60);
                    el.textContent = days > 0 ? `Ends in ${days}d ${hours}h ${m}m ${s}s` :
                        `Ends in ${hours}h ${m}m ${s}s`;
                    requestAnimationFrame(() => setTimeout(tick, 500));
                };
                tick();
            });

            // Guest fake 24h countdown (English units)
            const guestCountdown = document.getElementById('couponCountdownGuest');
            if (guestCountdown) {
                const end = new Date(guestCountdown.dataset.guestExp);
                const tickGuest = () => {
                    const diff = end - new Date();
                    if (diff <= 0) {
                        guestCountdown.textContent = 'Ends in 0h 0m 0s';
                        return;
                    }
                    const totalHours = Math.floor(diff / 1000 / 60 / 60);
                    const days = Math.floor(totalHours / 24);
                    const hours = totalHours % 24;
                    const m = Math.floor((diff / 1000 / 60) % 60);
                    const s = Math.floor((diff / 1000) % 60);

                    if (days > 0) {
                        guestCountdown.textContent = `Ends in ${days}d ${hours}h ${m}m ${s}s`;
                    } else {
                        guestCountdown.textContent = `Ends in ${hours}h ${m}m ${s}s`;
                    }
                    requestAnimationFrame(() => setTimeout(tickGuest, 500));
                };
                tickGuest();
            }
        })();
    </script>

    <script>
        // Promo bar copy + close
        document.addEventListener('DOMContentLoaded', () => {
            const promoBar = document.getElementById('couponPromobar');
            const promoClose = document.getElementById('promoClose');
            const promoToggle = document.getElementById('promoToggle');
            promoClose?.addEventListener('click', () => {
                promoBar?.classList.add('d-none');
                promoToggle?.classList.remove('d-none');
            });
            const showPromo = () => {
                promoBar?.classList.remove('d-none');
                promoToggle?.classList.add('d-none');
            };
            document.getElementById('couponFab')?.addEventListener('click', showPromo);
            promoToggle?.addEventListener('click', showPromo);
            document.querySelectorAll('.btn-copy').forEach(btn => {
                btn.addEventListener('click', () => {
                    const code = btn.dataset.code || '';
                    if (!code) return;
                    navigator.clipboard.writeText(code).then(() => {
                        btn.textContent = 'Copied';
                        setTimeout(() => btn.textContent = 'Copy', 1200);
                    });
                });
            });
        });
    </script>

    <script>
        // Lightweight event tracking: add data-track-action="buy_click" (and optional data-track-meta='{"id":123}')
        document.addEventListener('click', (e) => {
            const el = e.target.closest('[data-track-action]');
            if (!el) return;

            const action = el.dataset.trackAction;
            const metaRaw = el.dataset.trackMeta || '{}';
            let meta = {};
            try {
                meta = JSON.parse(metaRaw);
            } catch (_) {}

            fetch("{{ route('events.track') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: action,
                    path: window.location.pathname,
                    referrer: document.referrer || null,
                    meta: meta
                })
            }).catch(() => {});
        });
    </script>

    @php $recaptchaSiteKey = env('RECAPTCHA_SITE_KEY'); @endphp
    @if ($recaptchaSiteKey)
        <script>
            window.__recaptchaActiveForm = null;

            function onSubmitRecaptcha(token) {
                const form = window.__recaptchaActiveForm;
                if (!form) return;
                if (!form.checkValidity()) {
                    form.reportValidity();
                    resetRecaptchaButton();
                    return;
                }
                let tokenInput = form.querySelector('input[name="g-recaptcha-response"]');
                if (!tokenInput) {
                    tokenInput = document.createElement('input');
                    tokenInput.type = 'hidden';
                    tokenInput.name = 'g-recaptcha-response';
                    form.appendChild(tokenInput);
                }
                tokenInput.value = token;
                form.submit();
            }

            function resetRecaptchaButton() {
                const form = window.__recaptchaActiveForm;
                if (!form) return;
                const btn = form.querySelector('[data-sitekey]');
                if (!btn) return;
                btn.disabled = false;
                if (btn.dataset.originalText) {
                    btn.textContent = btn.dataset.originalText;
                }
            }

            function onRecaptchaError() {
                resetRecaptchaButton();
                console.error('recaptcha error-callback fired (implicit). Check domain/sitekey.');
            }

            function renderRecaptchaForms() {
                const forms = Array.from(document.querySelectorAll('form.js-recaptcha'));

                forms.forEach(form => {
                    const btn = form.querySelector('[data-sitekey]');
                    if (!btn) return;
                    if (btn.dataset.recaptchaBound === '1') return;
                    btn.dataset.recaptchaBound = '1';

                    let widgetId = null;

                    const ensureWidget = () => {
                        if (widgetId !== null) return widgetId;
                        if (typeof grecaptcha === 'undefined') return null;
                        widgetId = grecaptcha.render(btn, {
                            sitekey: btn.dataset.sitekey,
                            size: btn.dataset.size || 'invisible',
                            badge: btn.dataset.badge || 'bottomright',
                            callback: onSubmitRecaptcha,
                            'error-callback': onRecaptchaError,
                        });
                        return widgetId;
                    };

                    form.addEventListener('submit', (e) => {
                        e.preventDefault();
                        if (!form.checkValidity()) {
                            form.reportValidity();
                            return;
                        }

                        const id = ensureWidget();
                        if (id === null) {
                            onRecaptchaError();
                            return;
                        }

                        window.__recaptchaActiveForm = form;
                        btn.disabled = true;
                        btn.dataset.originalText = btn.dataset.originalText || btn.textContent;
                        btn.textContent = btn.dataset.loadingText || 'Sending...';
                        grecaptcha.execute(id);
                    });
                });
            }

            window.renderRecaptchaForms = renderRecaptchaForms;

            document.addEventListener('DOMContentLoaded', () => {
                renderRecaptchaForms();
            });
        </script>
        <script src="https://www.google.com/recaptcha/api.js?onload=renderRecaptchaForms&render=explicit" async defer></script>
    @endif

    <!-- Start of Tawk.to Script -->
    <script type="text/javascript">
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/692733040d028919595429ca/1jb0huih7';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    </script>
    <!-- End of Tawk.to Script -->

</body>

</html>
