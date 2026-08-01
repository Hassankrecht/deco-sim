@extends('layouts.app')

@section('meta_description', __('messages.meta.home_description'))
@section('content')
    @php
        $hs = $homeSettings ?? null;
        $heroType = $hs->hero_media_type ?? 'image';
        $heroImg = $hs?->hero_image_path ? asset($hs->hero_image_path) : asset('assets/img/video.jpg');
        $heroVideo = $hs?->hero_video_path
            ? asset($hs->hero_video_path)
            : ($hs?->hero_video_url ?:
            asset('assets/videos/last.mp4'));
        $heroHeight = 620; // ثابت للمظهر المتناسق
        $heroWidth = null;
        $heroBgColor = $hs->hero_bg_color ?? '#0b1220';
        $heroAutoFit = $hs->hero_auto_fit ?? true;
        $heroStretch = $hs->hero_stretch ?? true;
        $heroTitleSize = $hs->hero_title_size ?? null;
        $heroSubtitleSize = $hs->hero_subtitle_size ?? null;
        $heroButtonSize = $hs->hero_button_size ?? null;
        $heroContentPosX = $hs->hero_content_pos_x ?? 10;
        $heroContentPosY = $hs->hero_content_pos_y ?? 20;
        $heroTitleColor = $hs->hero_title_color ?? '#ffffff';
        $heroSubtitleColor = $hs->hero_subtitle_color ?? '#ffffff';
        $showTitle = $hs->show_title ?? true;
        $showSubtitle = $hs->show_subtitle ?? true;
        $heroBgSize = $hs->hero_bg_size ?? 100;
        $heroBgPosX = $hs->hero_bg_pos_x ?? 50;
        $heroBgPosY = $hs->hero_bg_pos_y ?? 50;
        $overlayEnabled = $hs->overlay_enabled ?? true;
        $overlayColor = $hs->overlay_color ?? '#000000';
        $overlayOpacity = ($hs->overlay_opacity ?? 65) / 100;
        $heroTitle = $hs->hero_title ?? __('messages.home.hero_title');
        $heroSubtitle = $hs->hero_subtitle ?? __('messages.home.hero_subtitle');
        $titleFont = $hs->hero_title_font ?? null;
        $subtitleFont = $hs->hero_subtitle_font ?? null;
        $bannerEnabled = $hs->banner_enabled ?? false;
        $bannerText = $hs->banner_text ?? null;
        $bannerLink = $hs->banner_link ?? null;
        $bannerImage = $hs?->banner_image_path ? asset($hs->banner_image_path) : null;
        $heroGalleryAll = collect($hs->hero_gallery ?? [])
            ->filter()
            ->map(fn($p) => asset($p));
        $heroGallery = $heroGalleryAll->count() >= 2 ? $heroGalleryAll : collect();
        $primaryColor = $hs->primary_color ?? null;
        $secondaryColor = $hs->secondary_color ?? null;
        $btnPrimaryText = $hs->btn_primary_text ?? __('messages.home.cta_projects');
        $btnPrimaryLink = $hs->btn_primary_link ?? '#projects';
        $btnPrimaryColor = $hs->btn_primary_color ?? '#c7954b';
        $btnPrimaryStyle = $hs->btn_primary_style ?? 'solid';
        $btnPrimaryVisible = $hs->btn_primary_visible ?? true;
        $btnSecondaryText = $hs->btn_secondary_text ?? __('messages.home.cta_quote');
        $btnSecondaryLink = $hs->btn_secondary_link ?? '#contact';
        $btnSecondaryColor = $hs->btn_secondary_color ?? '#ffffff';
        $btnSecondaryStyle = $hs->btn_secondary_style ?? 'outline';
        $btnSecondaryVisible = $hs->btn_secondary_visible ?? true;
    @endphp
    {{-- font reset: no custom body font --}}
    @if ($primaryColor || $secondaryColor)
        <style>
            :root {
                @if ($primaryColor)
                    --akg-gold: {{ $primaryColor }};
                @endif
                @if ($secondaryColor)
                    --akg-dark: {{ $secondaryColor }};
                @endif
            }
        </style>
    @endif
    {{-- ================= HERO ================= --}}
    <section class="akg-hero container-xxl d-flex align-items-center akg-hero-ma "
        style=" min-height: {{ $heroHeight }}px; background: {{ $heroBgColor }}; {{ $heroWidth ? 'max-width:' . $heroWidth . 'px; margin-left:auto; margin-right:auto;' : '' }}">

        @if ($heroType === 'video')
            <video class="akg-hero-video" autoplay loop muted playsinline poster="{{ $heroImg }}"
                style="min-height: {{ $heroHeight }}px; max-height: 720px; min-height: 480px; width:100%; object-fit: {{ $heroStretch ? 'fill' : 'contain' }}; background: {{ $heroBgColor }}; z-index:1;">
                <source src="{{ $heroVideo }}" type="video/mp4">
            </video>
        @else
            @if ($heroGallery->count() > 0)
                <div id="heroCarousel" class="carousel slide w-100 h-100 position-absolute" data-bs-ride="carousel">
                    <div class="carousel-inner h-100">
                        @foreach ($heroGallery as $idx => $img)
                            <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}" data-bs-interval="5000">
                                <img src="{{ $img }}" class="w-100 h-100 d-block"
                                    style="object-fit: {{ $heroStretch ? 'fill' : 'contain' }}; background: {{ $heroBgColor }};">
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <img src="{{ $heroImg }}" class="w-100 h-100 d-block"
                    style="object-fit: {{ $heroStretch ? 'fill' : 'contain' }}; background: {{ $heroBgColor }}; position:absolute; inset:0; z-index:1;">
            @endif
        @endif

        @if ($overlayEnabled)
            <div class="akg-hero-overlay"
                style="background: {{ $overlayColor }}; opacity: {{ $overlayOpacity }}; z-index:2; pointer-events:none;">
            </div>
        @endif

        <div class="akg-hero-content position-absolute text-light"
            style="top: {{ $heroContentPosY ?? 20 }}%; inset-inline-start: {{ $heroContentPosX ?? 10 }}%; max-width:60%; z-index:3; padding:0; text-align:start;">

            @if ($showTitle)
                <h1 class="akg-hero-title mb-3"
                    style="color: {{ $heroTitleColor }}; {{ $heroTitleSize ? 'font-size:' . $heroTitleSize . 'px;' : '' }}{{ $titleFont ? ' font-family:' . $titleFont . ';' : '' }}">
                    {{ $heroTitle }}
                </h1>
            @endif

            @if ($showSubtitle)
                <p class="akg-hero-subtitle mb-4"
                    style="white-space: pre-line; color: {{ $heroSubtitleColor }}; {{ $heroSubtitleSize ? 'font-size:' . $heroSubtitleSize . 'px;' : '' }}{{ $subtitleFont ? ' font-family:' . $subtitleFont . ';' : '' }}">
                    {{ $heroSubtitle }}
                </p>
            @endif

            <div class="d-flex gap-3 flex-wrap">
                @if ($btnPrimaryVisible)
                    @php
                        $primaryRadius = $btnPrimaryStyle === 'pill' ? '999px' : '6px';
                        $primaryBg = $btnPrimaryStyle === 'outline' ? 'transparent' : $btnPrimaryColor;
                        $primaryTextColor = $btnPrimaryStyle === 'outline' ? $btnPrimaryColor : '#0f172a';
                    @endphp
                    <a href="{{ $btnPrimaryLink }}" class="btn px-4 py-2"
                        style="background: {{ $primaryBg }}; border:1px solid {{ $btnPrimaryColor }}; color: {{ $primaryTextColor }}; border-radius: {{ $primaryRadius }}; {{ $heroButtonSize ? 'font-size:' . $heroButtonSize . 'px;' : '' }}">
                        {{ $btnPrimaryText }}
                    </a>
                @endif
                @if ($btnSecondaryVisible)
                    @php
                        $secondaryRadius = $btnSecondaryStyle === 'pill' ? '999px' : '6px';
                        $secondaryBg = $btnSecondaryStyle === 'solid' ? $btnSecondaryColor : 'transparent';
                        $secondaryTextColor = $btnSecondaryStyle === 'solid' ? '#0f172a' : $btnSecondaryColor;
                        $secondaryBorder = $btnSecondaryColor;
                    @endphp
                    <a href="{{ $btnSecondaryLink }}" class="btn px-4 py-2"
                        style="background: {{ $secondaryBg }}; border:1px solid {{ $secondaryBorder }}; color: {{ $secondaryTextColor }}; border-radius: {{ $secondaryRadius }}; {{ $heroButtonSize ? 'font-size:' . $heroButtonSize . 'px;' : '' }}">
                        {{ $btnSecondaryText }}
                    </a>
                @endif
            </div>
        </div>

    </section>
    @if ($bannerEnabled && $bannerText)
        <div class="modal fade" id="heroBannerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-dark text-light border border-gold">
                    <div class="modal-header border-0">
                        <h6 class="modal-title text-gold mb-0">Notice</h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body d-flex flex-column gap-3">
                        @if ($bannerImage)
                            <img src="{{ $bannerImage }}" alt="Banner" class="w-100 rounded" style="object-fit: cover;">
                        @endif
                        <div class="fw-bold">{{ $bannerText }}</div>
                        @if ($bannerLink)
                            <div>
                                <a href="{{ $bannerLink }}" class="btn btn-gold text-dark">Learn more</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                if (typeof bootstrap !== 'undefined') {
                    const modalEl = document.getElementById('heroBannerModal');
                    const bannerModal = new bootstrap.Modal(modalEl, {
                        backdrop: 'static'
                    });
                    bannerModal.show();
                }
            });
        </script>
    @endif

    {{-- ================= TRUST BAR ================= --}}
    <section class="container-xxl py-5">
        <div class="container akg-newcard">
            <div class="row g-3 text-center">
                <div class="col-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">35+</span>
                        <span class="akg-trust-label">
                            {{ __('messages.home.trust_years') }}
                        </span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">50+</span>
                        <span class="akg-trust-label">
                            {{ __('messages.home.trust_projects') }}
                        </span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">
                            {{ __('messages.home.trust_scope_number') }}
                        </span>
                        <span class="akg-trust-label">
                            {{ __('messages.home.trust_scope') }}
                        </span>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="akg-card text-center">
                        <span class="akg-trust-number">24/7</span>
                        <span class="akg-trust-label">
                            {{ __('messages.home.trust_support') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('services') }}" class="btn btn-gold px-4 py-2">
                    {{ __('messages.services_page.all_services') }}
                </a>
            </div>
        </div>
    </section>

    {{-- ================= WHY CHOOSE US ================= --}}
    <section class="container-xxl py-5 ">
        <div class="container akg-newcard text-center py-5">

            <h2 class="akg-section-head mb-5">
                {{ __('messages.home.why.title') }}
            </h2>

            <div class="row g-4">

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card">
                        <i class="fa fa-award akg-card-icon"></i>
                        <h4 class="text-gold">{{ __('messages.home.why.quality_title') }}</h4>
                        <p class="text-muted">{{ __('messages.home.why.quality_desc') }}</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card">
                        <i class="fa fa-users-gear akg-card-icon"></i>
                        <h4 class="text-gold">{{ __('messages.home.why.team_title') }}</h4>
                        <p class="text-muted">{{ __('messages.home.why.team_desc') }}</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card">
                        <i class="fa fa-clock akg-card-icon"></i>
                        <h4 class="text-gold">{{ __('messages.home.why.delivery_title') }}</h4>
                        <p class="text-muted">{{ __('messages.home.why.delivery_desc') }}</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="akg-card">
                        <i class="fa fa-handshake akg-card-icon"></i>
                        <h4 class="text-gold">{{ __('messages.home.why.trust_title') }}</h4>
                        <p class="text-muted">{{ __('messages.home.why.trust_desc') }}</p>
                    </div>
                </div>

            </div>
            <div class="mt-4">
                <a href="{{ route('contact') }}" class="btn btn-gold px-4 py-2">{{ __('messages.home.why.cta') }}</a>
            </div>

        </div>
    </section>
    {{-- ================= SERVICES ================= --}}

    <section id="services" class="container-xxl py-5">
        <div class="container akg-newcard">

            <h2 class="akg-section-head text-center mb-5">
                {{ __('messages.home.services_title') }}
            </h2>

            @php
                // جلب الخدمات من المتغير القادم من الكنترولر، وإن لم يتوفر نترجم من config مباشرة
                $homeServices = collect($services ?? [])
                    ->whenEmpty(function () {
                        return collect(config('services', []))->map(function ($s) {
                            $key = $s['translation_key'] ?? null;
                            return [
                                'slug' => $s['slug'] ?? '',
                                'icon' => $s['icon'] ?? null,
                                'title' => $key ? __($key . '.title') : '',
                                'excerpt' => $key ? __($key . '.excerpt') : '',
                            ];
                        });
                    })
                    ->take(4);
            @endphp

            <div class="row g-4">
                @foreach ($homeServices as $service)
                    <div class="col-lg-3 col-md-6">
                        <div class="akg-card text-center h-100">

                            {{-- ICON (optional) --}}
                            @if (!empty($service['icon']))
                                <i class="fa {{ $service['icon'] }} akg-card-icon"></i>
                            @endif

                            {{-- TITLE --}}
                            <h5 class="mt-3 text-gold">
                                {{ $service['title'] ?? '' }}
                            </h5>

                            {{-- EXCERPT --}}
                            <p class="text-muted small mb-3" style="min-height: 48px;">
                                {{ \Illuminate\Support\Str::limit($service['excerpt'] ?? '', 90) }}
                            </p>

                            {{-- CTA --}}
                            @php
                                $viewMoreText = __('messages.services_page.view_more');
                                if ($viewMoreText === 'messages.services_page.view_more') {
                                    $viewMoreText = app()->getLocale() === 'ar' ? 'عرض المزيد' : 'View More';
                                }
                            @endphp
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('services.show', $service['slug']) }}"
                                    class="btn btn-outline-gold btn-sm fw-bold">
                                    {{ $viewMoreText }}
                                </a>
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            {{-- BUTTON --}}
            @php
                $allServicesText = __('messages.services_page.all_services');
            @endphp
            <div class="text-center mt-4">
                <a href="{{ route('services') }}" class="btn btn-gold px-4 py-2">
                    {{ $allServicesText }}
                </a>
            </div>

        </div>
    </section>
    {{-- end services --}}

    {{-- ================= PROJECTS ================= --}}
    <section id="projects" class="container-xxl py-5 ">
        <div class="container akg-newcard">
            <h2 class="akg-section-head text-center">
                {{ __('messages.home.projects_title') }}
            </h2>
            <p class="text-center text-muted mb-5">
                {{ __('messages.home.projects_sub') }}
            </p>

            @php
                $projectParents = $projectParents ?? collect();
                $projectChildren = $projectChildren ?? collect();
                $activeProjParent = $projectParents->first()->id ?? null;
                $activeProjChild =
                    optional($projectParents->first()?->children?->first())->id ??
                    optional($projectChildren->first())->id;
            @endphp

            @if ($projectParents->count())
                <div class="akg-newcard mb-4 p-3 text-center akg-cat-nav">
                    <ul class="nav nav-pills justify-content-center mb-3 flex-wrap gap-2" id="projParentNav">
                        @foreach ($projectParents as $parent)
                            <li class="nav-item">
                                <a class="nav-link {{ $activeProjParent === $parent->id ? 'active' : '' }}"
                                    href="#" data-parent="{{ $parent->id }}">
                                    {{ $parent->name_localized }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @foreach ($projectParents as $parent)
                        <ul class="nav nav-pills justify-content-center flex-wrap gap-2 proj-child {{ $activeProjParent === $parent->id ? '' : 'd-none' }}"
                            id="proj-child-{{ $parent->id }}">
                            @forelse($parent->children as $child)
                                <li class="nav-item">
                                    <a class="nav-link {{ $child->id === $activeProjChild ? 'active' : '' }}"
                                        data-bs-toggle="pill" href="#proj-tab-{{ $child->id }}">
                                        {{ $child->name_localized }}
                                    </a>
                                </li>
                            @empty
                                <li class="nav-item">
                                    <span class="nav-link">{{ __('messages.projects.all') ?? 'All' }}</span>
                                </li>
                            @endforelse
                        </ul>
                    @endforeach
                </div>
            @endif

            <div class="tab-content">
                @forelse($projectChildren as $child)
                    @php
                        $projList = $projectsByCategory[$child->id] ?? collect();
                        $resolvePath = function ($path) {
                            if (!$path) {
                                return null;
                            }
                            // مسارات قديمة مُخزنة كـ storage/public/assets/... نصححها إلى assets/...
                            if (str_contains($path, 'storage/public/assets/')) {
                                $path = str_replace('storage/public/', '', $path);
                            }
                            return str_starts_with($path, 'public/') || str_starts_with($path, 'assets/')
                                ? asset($path)
                                : asset('storage/' . $path);
                        };
                    @endphp
                    <div id="proj-tab-{{ $child->id }}"
                        class="tab-pane fade {{ $child->id === $activeProjChild ? 'show active' : '' }}">
                        <div class="row g-4">
                            @forelse($projList as $project)
                                @php
                                    $imagePath =
                                        $project->main_image ?? ($project->images->first()->image_path ?? null);
                                    $img =
                                        $resolvePath($imagePath) ??
                                        ($project->main_image_url ?? asset('assets/img/default.jpg'));
                                    $gallery =
                                        collect($project->gallery_urls ?? [])
                                            ->filter()
                                            ->values() ?:
                                        $project->images
                                            ->pluck('image_path')
                                            ->map(fn($g) => $resolvePath($g))
                                            ->filter()
                                            ->values();
                                @endphp
                                <div class="col-lg-4 col-md-6 ">
                                    <div class="akg-card h-100">
                                        <img src="{{ $img }}" class="akg-project-img"
                                            alt="{{ $project->title }}" loading="lazy">

                                        <div class="p-3 text-center">
                                            <h4 class="text-gold">{{ $project->title_localized }}</h4>
                                            <p class="small text-muted">
                                                {{ Str::limit($project->description_localized, 80) }}
                                            </p>

                                            <button class="btn btn-outline-gold px-4 mt-2 view-project-btn"
                                                data-id="{{ $project->id }}"
                                                data-title="{{ e($project->title_localized) }}"
                                                data-description="{{ e($project->description_localized) }}"
                                                data-image="{{ $img }}"
                                                data-gallery='@json($gallery)'>
                                                {{ __('messages.home.projects_view') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="akg-card text-center py-4">
                                        <p class="text-muted mb-0">
                                            {{ __('messages.projects.no_projects') ?? 'No projects available.' }}</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="row g-4 j">
                        @forelse($projects as $project)
                            @php
                                $imagePath = $project->main_image ?? ($project->images->first()->image_path ?? null);
                                $img =
                                    $resolvePath($imagePath) ??
                                    ($project->main_image_url ?? asset('assets/img/default.jpg'));
                                $gallery = $project->images
                                    ->pluck('image_path')
                                    ->map(fn($imgPath) => $resolvePath($imgPath))
                                    ->filter()
                                    ->values();
                            @endphp

                            <div class="col-lg-4 col-md-6">
                                <div class="akg-card h-100">
                                    <img src="{{ $img }}" class="akg-project-img" alt="{{ $project->title }}"
                                        loading="lazy">

                                    <div class="p-3 text-center">
                                        <h4 class="text-gold">{{ $project->title_localized }}</h4>
                                        <p class="small text-muted">
                                            {{ Str::limit($project->description_localized, 80) }}
                                        </p>

                                        <button class="btn btn-outline-gold px-4 mt-2 view-project-btn"
                                            data-id="{{ $project->id }}"
                                            data-title="{{ e($project->title_localized) }}"
                                            data-description="{{ e($project->description_localized) }}"
                                            data-image="{{ $img }}"
                                            data-gallery='@json($gallery)'>
                                            {{ __('messages.home.projects_view') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-light">
                                {{ __('messages.projects.no_projects') ?? 'No projects available.' }}</p>
                        @endforelse
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-4">
                <a href="{{ url('/projects') }}" class="btn btn-gold px-4 py-2">
                    {{ __('messages.home.projects_all') }}
                </a>
            </div>
        </div>
    </section>

    <!-- Project Modal -->
    <div id="projectModal" class="modal fade" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark-soft text-light rounded-4 border border-gold">

                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-gold" id="projectTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="lightboxGallery" class="d-flex flex-wrap justify-content-center gap-3"></div>
                    <p id="projectDescription" class="mt-4 text-muted"></p>
                </div>

                <div class="modal-footer border-0 justify-content-between">
                    <button class="btn btn-outline-gold" data-bs-dismiss="modal">{{ __('messages.projects.close') }}</button>
                </div>

            </div>
        </div>
    </div>

    <!-- Lightbox Overlay -->
    <div id="lightboxOverlay">
        <div class="lightbox-content">
            <img id="lightboxImage" src="">
            <span class="lightbox-close">&times;</span>
            <button class="lightbox-prev">&#10094;</button>
            <button class="lightbox-next">&#10095;</button>
        </div>
    </div>








    {{-- ================= ABOUT ================= --}}
    <section id="about" class="container-xxl py-5">
        <div class="container akg-newcard">
            <div class="row g-5 align-items-center">

                <!-- IMAGES SIDE -->
                <div class="col-lg-6">
                    <div class="row g-3 about-lux-grid">

                        <div class="col-6 d-flex align-items-end">
                            <img src="{{ asset('assets/img/pexels-cottonbro-7492889.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Craftsman at work"
                                style="height: 280px; object-fit: cover;" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-end">
                            <img src="{{ asset('assets/img/pexels-pixabay-159375.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Premium carpentry tools"
                                style="height: 240px; object-fit: cover; margin-top: 40px;" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-start">
                            <img src="{{ asset('assets/img/pexels-enginakyurt-1463917.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Luxury interior details"
                                style="height: 240px; object-fit: cover; margin-bottom: 40px;" loading="lazy">
                        </div>

                        <div class="col-6 d-flex align-items-start">
                            <img src="{{ asset('assets/img/pexels-ivan-s-4491884.jpg') }}"
                                class="img-fluid rounded w-100 shadow-sm" alt="Custom wood paneling"
                                style="height: 280px; object-fit: cover;" loading="lazy">
                        </div>

                    </div>
                </div>

                <!-- TEXT SIDE -->
                <div class="col-lg-6">
                    <h5 class="akg-hero-title text-gold mb-3">
                        {{ __('messages.home.about_title') }}
                    </h5>

                    <h2 class="text-light mb-4">
                        {{ __('messages.home.about.heading_full') }}
                    </h2>

                    <p class="text-muted mb-3">
                        {{ __('messages.home.about.paragraph1') }}
                    </p>

                    <p class="text-muted mb-4">
                        {{ __('messages.home.about.paragraph2') }}
                    </p>

                    <div class="akg-why-box">
                        <div class="akg-why-item">
                            <i class="fa fa-check text-gold me-2"></i>
                            {{ __('messages.home.about.why_1') }}
                        </div>
                        <div class="akg-why-item">
                            <i class="fa fa-check text-gold me-2"></i>
                            {{ __('messages.home.about.why_2') }}
                        </div>
                        <div class="akg-why-item">
                            <i class="fa fa-check text-gold me-2"></i>
                            {{ __('messages.home.about.why_3') }}
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-sm-6">
                            <div class="experience-box">
                                <h1 class="experience-number">35</h1>
                                <div class="ps-3">
                                    <p class="mb-0 small text-muted">
                                        {{ __('messages.home.about.experience_years') }}
                                    </p>
                                    <h6 class="text-uppercase text-light mb-0">
                                        {{ __('messages.home.about.experience_label') }}
                                    </h6>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="experience-box">
                                <h1 class="experience-number">50</h1>
                                <div class="ps-3">
                                    <p class="mb-0 small text-muted">
                                        {{ __('messages.home.about.projects_success') }}
                                    </p>
                                    <h6 class="text-uppercase text-light mb-0">
                                        {{ __('messages.home.about.projects_label') }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('about') }}" class="btn btn-gold px-4 mt-2">
                            {{ __('messages.home.about_cta_learn') }}
                        </a>
                        <a href="#contact" class="btn btn-outline-gold px-4 mt-2">
                            {{ __('messages.home.about_cta_visit') }}
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ================= TESTIMONIALS ================= --}}
    <section id="testimonials" class="container-xxl py-5">
        <div class="container akg-newcard text-center">

            <h5 class="akg-section-label">
                {{ __('messages.home.testimonials_label') }}
            </h5>
            <h2 class="akg-section-head mb-5">
                {{ __('messages.home.testimonials_title') }}
            </h2>
            <p class="text-muted mb-4">
                {{ __('messages.home.rating_text') }}:
                <span class="text-gold fw-bold">
                    {{ $reviewsCount ? number_format($reviewsAvg, 1) : '—' }} / 5
                </span>
                @if ($reviewsCount)
                    <small class="text-muted">({{ $reviewsCount }}
                        {{ __('messages.home.testimonials_label') }})</small>
                @endif
            </p>

            @if (session('review_success'))
                <div class="alert alert-success text-start">
                    {{ session('review_success') }}
                </div>
            @endif

            <div class="row g-4 justify-content-center">
                @forelse($reviews as $review)
                    @php
                        // الصور الجديدة تحفظ في public/assets/reviews، والقديمة في storage/public أو storage
                        if ($review->photo) {
                            $path = $review->photo;

                            // تطبيع مسارات قديمة
                            if (str_contains($path, 'storage/public/assets/reviews/')) {
                                $path = str_replace('storage/', '', $path); // -> public/assets/...
                            }

                            // جرّب مباشرة public/ أو assets/
                            if (str_starts_with($path, 'public/')) {
                                $candidate = public_path(substr($path, strlen('public/')));
                                $photo = file_exists($candidate)
                                    ? asset($path)
                                    : asset(substr($path, strlen('public/')));
                            } elseif (str_starts_with($path, 'assets/')) {
                                $candidate = public_path($path);
                                $photo = file_exists($candidate)
                                    ? asset($path)
                                    : asset('assets/img/clients/client1.jpg');
                            } else {
                                // مسارات مخزنة كـ reviews/.. داخل storage
                                $photo = asset('storage/' . $path);
                            }
                        } else {
                            $photo = asset('assets/img/clients/client1.jpg');
                        }
                    @endphp
                    <div class="col-lg-4 col-md-6">
                        <div class="akg-card text-center h-100">
                            <div class="akg-testimonial-icon">
                                <i class="fa fa-quote-left"></i>
                            </div>
                            <div class="akg-testimonial-stars mb-2">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= $review->rating)
                                        <i class="fa fa-star"></i>
                                    @else
                                        <i class="fa fa-star-o"></i>
                                    @endif
                                @endfor
                            </div>

                            <p class="akg-testimonial-text">
                                “{{ $review->review }}”
                            </p>

                            <div class="akg-testimonial-client mt-3">
                                <img src="{{ $photo }}" class="akg-client-img"
                                    alt="Client - {{ $review->name }}" loading="lazy">
                                <h6 class="text-gold mt-2">{{ $review->name }}</h6>
                                <span class="text-muted small">{{ $review->profession }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="akg-card text-center py-4">
                            <h6 class="text-gold mb-1">{{ __('messages.home.testimonials_label') }}</h6>
                            <p class="text-muted mb-0">{{ __('messages.home.projects_sub') }}</p>
                        </div>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                <button class="btn btn-gold px-5" data-bs-toggle="modal" data-bs-target="#reviewModal">
                    {{ __('messages.home.testimonial_form.open_btn') }}
                </button>
            </div>
        </div>
    </section>

    <!-- Review Modal -->
    <style>
        .review-modal .modal-content {
            background: #0b1118;
            border: 1px solid rgba(212, 175, 55, 0.6);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.45);
        }

        .review-modal .form-control,
        .review-modal .form-control:focus,
        .review-modal textarea,
        .review-modal textarea:focus {
            background: #0f172a;
            color: #f8fafc;
            border-color: rgba(212, 175, 55, 0.45);
            box-shadow: none;
        }

        .review-modal .form-control::placeholder,
        .review-modal textarea::placeholder {
            color: #94a3b8;
        }

        .review-modal .form-label {
            color: #e2e8f0;
        }

        .review-modal .text-muted {
            color: #a3b1c6 !important;
        }

        .review-modal .btn-close {
            filter: invert(1);
        }

        .akg-rating-stars {
            gap: 6px;
        }

        .akg-star-btn {
            border: 0;
            background: transparent;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease;
        }

        .akg-star-btn .fa {
            color: #7c8799 !important;
            font-size: 22px;
            transition: color 0.15s ease;
        }

        .akg-star-btn:hover {
            transform: translateY(-1px) scale(1.05);
        }

        .akg-star-btn.active .fa,
        .akg-star-btn.active i {
            color: #d4af37 !important;
        }
    </style>
    <div class="modal fade review-modal" id="reviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark-soft text-light">
                <div class="modal-header border-0">
                    <h5 class="modal-title text-gold">{{ __('messages.home.testimonial_form.title') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm" action="{{ route('reviews.store') }}" method="POST"
                        enctype="multipart/form-data" class="row g-3 js-recaptcha">
                        @csrf
                        <input type="hidden" name="g-recaptcha-response">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">{{ __('messages.home.testimonial_form.name') }}
                                *</label>
                            <input type="text" name="name" class="form-control akg-input" required>
                        </div>
                        <div class="col-md-6">
                            <label
                                class="form-label small text-muted">{{ __('messages.home.testimonial_form.profession') }}</label>
                            <input type="text" name="profession" class="form-control akg-input">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small text-muted">{{ __('messages.home.testimonial_form.rating') }}
                                *</label>
                            <div class="d-flex align-items-center gap-2 akg-rating-stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" class="akg-star-btn" data-value="{{ $i }}" aria-label="{{ $i }}">
                                        <i class="fa fa-star"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingInput" required value="5">
                            <small class="text-muted">{{ __('messages.home.testimonial_form.rating_hint') }}</small>
                        </div>
                        <div class="col-md-12">
                            <label
                                class="form-label small text-muted">{{ __('messages.home.testimonial_form.photo') }}</label>
                            <input type="file" name="photo" class="form-control akg-input" accept="image">
                            <small class="text-muted">{{ __('messages.home.testimonial_form.photo_hint') }}</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted">{{ __('messages.home.testimonial_form.review') }}
                                *</label>
                            <textarea name="review" rows="4" class="form-control akg-input" required></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-gold px-4"
                                data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}" data-size="invisible"
                                data-badge="bottomright">
                                {{ __('messages.home.testimonial_form.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= CONTACT ================= --}}
    <section id="contact" class="container-xxl py-5">
        <div class="container akg-newcard text-center">
            <h2 class="akg-section-head">
                {{ __('messages.home.contact_title') }}
            </h2>
            <p class="text-muted mb-3">
                {{ __('messages.home.contact_sub') }}
            </p>

            @if (session('success'))
                <div class="alert alert-success text-start">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any() && old('form_origin') === 'home-contact')
                <div class="alert alert-danger text-start">
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row justify-content-center mb-4">
                <div class="col-md-4">
                    <a class="akg-quick-contact d-inline-flex align-items-center justify-content-center"
                        href="tel:+244972100585">
                        <i class="fa fa-phone me-2"></i> +244 972 100 585
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="akg-quick-contact d-inline-flex align-items-center justify-content-center"
                        href="https://wa.me/244972100585" target="_blank" rel="noopener">
                        <i class="fab fa-whatsapp me-2"></i> WhatsApp available
                    </a>
                </div>
            </div>

            <div class="akg-card p-4">
                <form action="{{ route('contact.send') }}" method="POST" class="row g-3 mt-4 js-recaptcha"
                    id="homeContactForm">
                    @csrf
                    <input type="hidden" name="g-recaptcha-response">
                    <input type="hidden" name="subject"
                        value="{{ __('messages.home.contact_title') ?? 'Quick Inquiry' }} - Home">
                    <input type="hidden" name="form_origin" value="home-contact">

                    <div class="col-md-6">
                        <input type="text" name="name" class="form-control akg-input" placeholder="Your Name"
                            value="{{ old('name') }}" required>
                    </div>

                    <div class="col-md-6">
                        <input type="email" name="email" class="form-control akg-input" placeholder="Your Email"
                            value="{{ old('email') }}" required>
                    </div>

                    <div class="col-12">
                        <textarea name="message" class="form-control akg-input" rows="5" placeholder="Your Message" required>{{ old('message') }}</textarea>
                    </div>

                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-gold px-5 py-3"
                            data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}" data-size="invisible"
                            data-badge="bottomright" id="homeContactSubmit">
                            {{ __('messages.home.contact_send') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    {{-- ================= PRODUCTS ================= --}}
    <section id="products" class="container-xxl py-5">
        <div class="container akg-newcard">

            <div class="text-center mb-5">
                <h5 class="akg-section-label">
                    {{ __('messages.home.products_label') }}
                </h5>
                <h2 class="akg-section-head">
                    {{ __('messages.home.products_title') }}
                </h2>
            </div>

            @php
                $productDisplayCategories = $productDisplayCategories ?? $childCategories ?? collect();
                $activeProductCategoryId = optional($productDisplayCategories->first())->id;
            @endphp

            @if (isset($parentCategories) && $parentCategories->count())
                @php
                    $activeParent = $parentCategories->first()->id ?? null;
                    foreach ($parentCategories as $parent) {
                        $hasActiveChild = $parent->children->contains(fn($child) => $child->id === $activeProductCategoryId);
                        if ($parent->id === $activeProductCategoryId || $hasActiveChild) {
                            $activeParent = $parent->id;
                            break;
                        }
                    }
                @endphp
                <div class="akg-newcard mb-4 p-3 text-center akg-cat-nav">
                    <ul class="nav nav-pills justify-content-center mb-3 flex-wrap gap-2" id="prodParentNav">
                        @foreach ($parentCategories as $parent)
                            <li class="nav-item">
                                <a class="nav-link {{ $activeParent === $parent->id ? 'active' : '' }}" href="#"
                                    data-parent="{{ $parent->id }}">
                                    {{ $parent->name_localized }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @foreach ($parentCategories as $parent)
                        <ul class="nav nav-pills justify-content-center flex-wrap gap-2 prod-child {{ $activeParent === $parent->id ? '' : 'd-none' }}"
                            id="prod-child-{{ $parent->id }}">
                            @forelse($parent->children as $child)
                                <li class="nav-item">
                                    <a class="nav-link {{ $child->id === $activeProductCategoryId ? 'active' : '' }}"
                                        data-bs-toggle="pill" href="#tab-{{ $child->id }}">
                                        {{ $child->name_localized }}
                                    </a>
                                </li>
                            @empty
                                <li class="nav-item">
                                    <a class="nav-link {{ $parent->id === $activeProductCategoryId ? 'active' : '' }}"
                                        data-bs-toggle="pill" href="#tab-{{ $parent->id }}">
                                        {{ __('messages.products.all') }}
                                    </a>
                                </li>
                            @endforelse
                        </ul>
                    @endforeach
                </div>
            @endif

            <div class="tab-content">
                @forelse ($productDisplayCategories as $child)
                    @php
                        $products = $productsByCategory[$child->id] ?? collect();
                    @endphp
                    <div id="tab-{{ $child->id }}"
                        class="tab-pane fade {{ $child->id === ($activeProductCategoryId ?? null) ? 'show active' : '' }}">
                        <div class="row g-4 justify-content-center">

                            @forelse ($products as $product)
                                @php
                                    $resolvePath = function ($path) {
                                        if (!$path) {
                                            return null;
                                        }
                                        return str_starts_with($path, 'public/') || str_starts_with($path, 'assets/')
                                            ? asset($path)
                                            : asset('storage/' . $path);
                                    };

                                    $img =
                                        $resolvePath($product->image) ??
                                        ($product->images->first()
                                            ? $resolvePath($product->images->first()->image)
                                            : asset('assets/img/default.jpg'));
                                @endphp

                                <div class="col-xl-3 col-lg-4 col-md-6">
                                    <div class="akg-product-card">
                                        @if ($loop->first)
                                            <span class="akg-product-badge">
                                                {{ __('messages.products_misc.badge_new') }}
                                            </span>
                                        @endif

                                        <div class="akg-product-img-box">
                                            <img src="{{ $img }}" class="akg-product-img"
                                                alt="{{ $product->title_localized ?? $product->title }}" loading="lazy">
                                            <div class="akg-product-overlay">
                                                <a href="{{ route('products.show', $product->id) }}"
                                                    class="btn-gold-small">
                                                    {{ __('messages.home.products_view') }}
                                                </a>
                                            </div>
                                        </div>

                                        <div class="akg-product-info text-center">
                                            <h5 class="akg-product-title">
                                                {{ $product->title_localized }}
                                            </h5>
                                            <p class="akg-product-desc">
                                                {{ Str::limit($product->description_localized, 55) }}
                                            </p>
                                            <span class="akg-product-price">
                                                ${{ $product->price }}
                                            </span>
                                        </div>

                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="akg-card text-center py-4">
                                        <p class="text-muted mb-0">{{ __('messages.products.none') }}</p>
                                    </div>
                                </div>
                            @endforelse

                        </div>
                    </div>
                @empty
                    <div class="akg-card text-center py-4">
                        <p class="text-muted mb-0">{{ __('messages.products.none') }}</p>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

    {{-- ================= TEAM ================= --}}
    <section id="team" class="container-xxl py-5">
        <div class="container akg-newcard">

            <div class="text-center mb-5">
                <h5 class="akg-section-label">
                    {{ __('messages.home.team_label') }}
                </h5>
                <h2 class="akg-section-head">
                    {{ __('messages.home.team_title') }}
                </h2>
            </div>

            <div class="row g-4">
                @foreach ($team as $member)
                    <div class="col-lg-3 col-md-6">
                        <div class="akg-card">
                            <div class="akg-team-img-box">
                                <img src="{{ $member['photo'] }}" class="akg-team-img" alt="{{ $member['name'] }}"
                                    loading="lazy">
                            </div>

                            <div class="akg-team-info text-center mt-3">
                                <h5 class="akg-team-name">{{ $member['name'] }}</h5>
                                <p class="akg-team-job">{{ $member['job'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>


    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const modal = new bootstrap.Modal(document.getElementById('projectModal'));
            const modalTitle = document.getElementById('projectTitle');
            const modalDesc = document.getElementById('projectDescription');
            const galleryBox = document.getElementById('lightboxGallery');

            const lightbox = document.getElementById('lightboxOverlay');
            const lightboxImage = document.getElementById('lightboxImage');
            const nextBtn = document.querySelector('.lightbox-next');
            const prevBtn = document.querySelector('.lightbox-prev');
            const closeBtn = document.querySelector('.lightbox-close');

            let allImages = [];
            let currentIndex = 0;

            document.querySelectorAll('.view-project-btn').forEach(btn => {
                btn.addEventListener('click', () => {
                    const title = btn.dataset.title;
                    const desc = btn.dataset.description;
                    const image = btn.dataset.image;
                    const gallery = JSON.parse(btn.dataset.gallery || '[]');
                    modalTitle.textContent = title;
                    modalDesc.textContent = desc;

                    galleryBox.innerHTML = "";

                    allImages = [image, ...gallery];

                    allImages.forEach((src, index) => {
                        const img = document.createElement('img');
                        img.src = src;
                        img.classList.add('lightbox-thumb', 'rounded', 'shadow');
                        img.style.width = '170px';
                        img.style.height = '130px';
                        img.style.objectFit = 'cover';
                        img.onclick = () => openLightbox(index);
                        galleryBox.appendChild(img);
                    });

                    modal.show();
                });
            });

            function openLightbox(index) {
                currentIndex = index;
                lightboxImage.src = allImages[currentIndex];
                lightbox.style.display = 'flex';
                document.body.style.overflow = "hidden";
            }

            function closeLightbox() {
                lightbox.style.display = "none";
                document.body.style.overflow = "auto";
            }

            nextBtn.onclick = () => {
                currentIndex = (currentIndex + 1) % allImages.length;
                lightboxImage.src = allImages[currentIndex];
            };

            prevBtn.onclick = () => {
                currentIndex = (currentIndex - 1 + allImages.length) % allImages.length;
                lightboxImage.src = allImages[currentIndex];
            };

            closeBtn.onclick = closeLightbox;

            lightbox.onclick = (e) => {
                if (e.target === lightbox) closeLightbox();
            };

            // Rating stars in review modal
            const ratingInput = document.getElementById('ratingInput');
            const starButtons = document.querySelectorAll('.akg-star-btn');
            const setStars = (val) => {
                starButtons.forEach(s => {
                    const isActive = Number(s.dataset.value) <= Number(val);
                    s.classList.toggle('active', isActive);
                    const icon = s.querySelector('i');
                    if (icon) {
                        icon.style.color = isActive ? '#d4af37' : '#7c8799';
                    }
                });
            };

            if (ratingInput) {
                setStars(ratingInput.value || 5);
            }

            starButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    const val = btn.dataset.value;
                    ratingInput.value = val;
                    setStars(val);
                });
            });

            // Products parent/child nav sync
            const parentLinks = document.querySelectorAll('#prodParentNav .nav-link');
            parentLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const parentId = link.dataset.parent;

                    parentLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');

                    document.querySelectorAll('.prod-child').forEach(el => el.classList.add(
                        'd-none'));
                    const childRow = document.getElementById(`prod-child-${parentId}`);
                    if (childRow) {
                        childRow.classList.remove('d-none');
                        const firstChild = childRow.querySelector('.nav-link');
                        if (firstChild) firstChild.click();
                    }
                });
            });

            // Projects parent/child nav sync
            const projParentLinks = document.querySelectorAll('#projParentNav .nav-link');
            projParentLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const parentId = link.dataset.parent;

                    projParentLinks.forEach(l => l.classList.remove('active'));
                    link.classList.add('active');

                    document.querySelectorAll('.proj-child').forEach(el => el.classList.add(
                        'd-none'));
                    const childRow = document.getElementById(`proj-child-${parentId}`);
                    if (childRow) {
                        childRow.classList.remove('d-none');
                        const firstChild = childRow.querySelector('.nav-link');
                        if (firstChild) firstChild.click();
                    }
                });
            });

            // Quick inquiry form: leave reCAPTCHA to handle submit; no client-side disabling
        });
    </script>
@endsection
