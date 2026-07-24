@extends('layouts.app')

@section('title', __('messages.about_page.hero_title'))
@section('meta_description', 'Learn about Deco Sim, our mission, values, and expertise in luxury carpentry and interior design in Angola.')

@section('content')

    <!-- ===================== HERO SECTION (FULL IMAGE – NO CROP) ===================== -->
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 11_50_19 AM.png') }}" alt="{{ __('messages.about_page.hero_title') }}"
            class="akg-hero-img" loading="lazy">



        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.about_page.hero_title') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ __('messages.about_page.breadcrumb') }}</li>
            </ol>
        </div>
    </div>


    <!-- ===================== ABOUT SECTION ===================== -->
    <div class="container-xxl py-5">
        <div class="container about-item">
            <div class="row g-5 align-items-center">

                <!-- Images -->
                <div class="col-lg-6">
                    <div class="row gx-3 gy-3">

                        <div class="col-6 d-flex align-items-end">
                            <img src="{{ asset('assets/img/pexels-cottonbro-7492889.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" style="height: 280px; object-fit: cover;"
                                alt="Master carpenter crafting wood" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-end">
                            <img src="{{ asset('assets/img/pexels-pixabay-159375.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Premium carpentry tools"
                                style="height: 240px; object-fit: cover; margin-top: 40px;" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-start">
                            <img src="{{ asset('assets/img/pexels-enginakyurt-1463917.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Luxury interior detailing"
                                style="height: 240px; object-fit: cover; margin-bottom: 40px;" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-start">
                            <img src="{{ asset('assets/img/pexels-ivan-s-4491884.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Custom wood paneling"
                                style="height: 280px; object-fit: cover;" loading="lazy">
                        </div>

                    </div>
                </div>

                <!-- Text -->
                <div class="col-lg-6 about-text">
                    <h5 class="akg-section-label">{{ __('messages.about_page.about_label') }}</h5>

                    <h1 class="mb-4 text-gold">
                        {{ __('messages.about_page.about_head') }}
                    </h1>

                    <p class="text-light">
                        {{ __('messages.about_page.p1') }}
                    </p>

                    <p class="text-light">
                        {{ __('messages.about_page.p2') }}
                    </p>

                    <p class="text-muted">
                        {{ __('messages.about_page.p3') }}
                    </p>

                    <p class="text-warning fw-bold" style="font-size: 1.25rem;">
                        {{ __('messages.about_page.p_quote') }}
                    </p>

                    <!-- Trust stats -->
                    <div class="row g-3 mt-4">
                        <div class="col-sm-6 col-lg-3">
                            <div class="akg-card text-center">
                        <span class="akg-trust-number">35+</span>
                        <span class="akg-trust-label">{{ __('messages.about_page.trust_years') }}</span>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">50+</span>
                        <span class="akg-trust-label">{{ __('messages.about_page.trust_projects') }}</span>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">24/7</span>
                        <span class="akg-trust-label">{{ __('messages.about_page.trust_support') }}</span>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">Q/A</span>
                        <span class="akg-trust-label">{{ __('messages.about_page.trust_quality') }}</span>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-3 flex-wrap mt-4">
                <a href="{{ route('projects.index') }}" class="btn btn-gold px-4 py-2">{{ __('messages.home.projects_view') }}</a>
                <a href="{{ route('contact') }}" class="btn btn-outline-gold px-4 py-2">{{ __('messages.projects_page.cta_consult') }}</a>
            </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ===================== METHOD / PARTNERS ===================== -->
    <div class="container-xxl py-5">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-6">
                    <h5 class="akg-section-label">{{ __('messages.about_page.how_we_work_label') }}</h5>
                    <h2 class="akg-section-head mb-4">{{ __('messages.about_page.how_we_work_head') }}</h2>
                    <ul class="list-unstyled text-light small">
                        <li class="mb-3"><i class="fa fa-check text-gold me-2"></i>{{ __('messages.about_page.how_step1') }}</li>
                        <li class="mb-3"><i class="fa fa-check text-gold me-2"></i>{{ __('messages.about_page.how_step2') }}</li>
                        <li class="mb-3"><i class="fa fa-check text-gold me-2"></i>{{ __('messages.about_page.how_step3') }}</li>
                        <li><i class="fa fa-check text-gold me-2"></i>{{ __('messages.about_page.how_step4') }}</li>
                    </ul>
                </div>
                <div class="col-lg-6">
                    <div class="akg-card p-4">
                        <h5 class="text-gold mb-3">{{ __('messages.about_page.trusted_by') }}</h5>
                        <div class="d-flex flex-wrap gap-3 align-items-center">
                            <span class="badge bg-dark text-gold border border-gold">{{ __('messages.about_page.badge_hospitality') }}</span>
                            <span class="badge bg-dark text-gold border border-gold">{{ __('messages.about_page.badge_residential') }}</span>
                            <span class="badge bg-dark text-gold border border-gold">{{ __('messages.about_page.badge_retail') }}</span>
                            <span class="badge bg-dark text-gold border border-gold">{{ __('messages.about_page.badge_offices') }}</span>
                            <span class="badge bg-dark text-gold border border-gold">{{ __('messages.about_page.badge_developers') }}</span>
                        </div>
                        <p class="text-muted small mb-0 mt-3">{{ __('messages.about_page.trusted_note') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
