@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 text-gold"><i class="bi bi-house-door me-2"></i>{{ __('admin.home_settings') }}</h5>
                <p class="text-muted small mb-0">{{ __('admin.home_settings_subtitle') }}</p>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success small">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger small">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php
            $hs = $settings;
            $heroType = $hs->hero_media_type ?? ($hs?->hero_video_path ? 'video' : 'image');
            $heroImg = $hs?->hero_image_path ? asset($hs->hero_image_path) : asset('assets/img/video.jpg');
            $heroVideo = $hs?->hero_video_path ? asset($hs->hero_video_path) : $hs?->hero_video_url;
            $heroTitle = $hs->hero_title ?? 'Hero title';
            $heroSubtitle = $hs->hero_subtitle ?? 'Hero subtitle';
            $galleryAll = collect($hs->hero_gallery ?? [])
                ->filter()
                ->map(fn($p) => asset($p));
            $gallery = $galleryAll->count() >= 2 ? $galleryAll : collect();
            $heroHeight = 700; // أعلى قليلاً لعرض أوضح
            $heroWidth = $hs->hero_width ?? null;
            $heroBgColor = $hs->hero_bg_color ?? '#0b1220';
            $heroAutoFit = $hs->hero_auto_fit ?? true;
            $heroStretch = $hs->hero_stretch ?? true;
            $heroTitleSize = $hs->hero_title_size ?? null;
            $heroSubtitleSize = $hs->hero_subtitle_size ?? null;
            $heroButtonSize = $hs->hero_button_size ?? null;
            $heroTitleColor = $hs->hero_title_color ?? '#ffffff';
            $heroSubtitleColor = $hs->hero_subtitle_color ?? '#ffffff';
            $showTitle = $hs->show_title ?? true;
            $showSubtitle = $hs->show_subtitle ?? true;
            $heroContentPosX = $hs->hero_content_pos_x ?? 10;
            $heroContentPosY = $hs->hero_content_pos_y ?? 20;
            $heroBgSize = $hs->hero_bg_size ?? 100;
            $heroBgPosX = $hs->hero_bg_pos_x ?? 50;
            $heroBgPosY = $hs->hero_bg_pos_y ?? 50;
            $overlayEnabled = $hs->overlay_enabled ?? true;
            $overlayColor = $hs->overlay_color ?? '#000000';
            $overlayOpacity = ($hs->overlay_opacity ?? 65) / 100;
            $pStyle = $hs->btn_primary_style ?? 'solid';
            $sStyle = $hs->btn_secondary_style ?? 'outline';
            $pVisible = $hs->btn_primary_visible ?? true;
            $sVisible = $hs->btn_secondary_visible ?? true;
            $pRadius = $pStyle === 'pill' ? '999px' : '6px';
            $sRadius = $sStyle === 'pill' ? '999px' : '6px';
            $pBg = $pStyle === 'outline' ? 'transparent' : $hs->btn_primary_color ?? '#c7954b';
            $pColor = $pStyle === 'outline' ? $hs->btn_primary_color ?? '#c7954b' : '#0f172a';
            $sBg = $sStyle === 'solid' ? $hs->btn_secondary_color ?? '#ffffff' : 'transparent';
            $sColor = $sStyle === 'solid' ? '#0f172a' : $hs->btn_secondary_color ?? '#ffffff';
        @endphp

    <style>
        .miniHero-compact {
            position: fixed !important;
            bottom: 16px;
            right: 16px;
            width: 420px !important;
            height: 260px !important;
            max-width: 90vw;
            max-height: 50vh;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 1050;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }
        .miniHero-compact #miniContent {
            transform: scale(0.5);
            transform-origin: top left;
        }
        .miniHero-compact #miniContent .btn {
            padding: 4px 8px;
            font-size: 11px;
        }
        .miniHero-hidden {
            opacity: 0;
            pointer-events: none;
        }
        #miniHeroShow {
            position: fixed;
            bottom: 16px;
            right: 16px;
            z-index: 1040;
            display: none;
        }
        #miniHeroClose { display:block; }
    </style>
    <div class="mb-4">
            @php $heroBg = $gallery->first() ?? $heroImg; @endphp
            <div id="miniHero" class="position-relative overflow-hidden rounded-3 border"
                style="height:{{ $heroHeight }}px; background:{{ $heroBgColor }}; width:100%; max-width:1440px; margin:0 auto; border:2px solid rgba(255,255,255,0.15); box-shadow:0 8px 24px rgba(0,0,0,0.35);">
                <button type="button" id="miniHeroClose" class="btn btn-sm btn-dark position-absolute"
                    style="top:8px; right:8px; z-index:9;">×</button>
                <video id="miniHeroVideo" class="w-100 h-100 position-absolute" autoplay loop muted playsinline
                    src="{{ $heroVideo ?? '' }}"
                    style="object-fit:{{ $heroStretch ? 'fill' : 'contain' }}; background:{{ $heroBgColor }}; z-index:1; inset:0; {{ $heroType === 'video' ? '' : 'display:none;' }}"></video>
                <img id="miniHeroImg" src="{{ $heroBg }}" class="w-100 h-100 d-block"
                    style="object-fit: {{ $heroStretch ? 'fill' : 'contain' }}; background: {{ $heroBgColor }}; position:absolute; inset:0; z-index:1; {{ $heroType === 'video' || $gallery->count() > 1 ? 'display:none;' : '' }}">
                @if ($gallery->count() > 1)
                    <div id="miniHeroCarousel" class="carousel slide w-100 h-100 position-absolute" data-bs-ride="carousel"
                        data-bs-interval="4000" style="{{ $heroType === 'video' ? 'display:none;' : '' }}">
                        <div class="carousel-inner h-100" id="miniHeroCarouselInner">
                            @foreach ($gallery as $idx => $img)
                                <div class="carousel-item h-100 {{ $idx === 0 ? 'active' : '' }}">
                                    <img src="{{ $img }}" class="w-100 h-100 d-block"
                                        style="object-fit: {{ $heroStretch ? 'fill' : 'contain' }}; background: {{ $heroBgColor }};">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div id="miniOverlay"
                    style="position:absolute; inset:0; background:{{ $overlayColor }}; opacity: {{ $overlayOpacity }}; z-index:2; pointer-events:none; {{ $overlayEnabled ? '' : 'display:none;' }}">
                </div>
                <div class="position-absolute top-0 end-0 p-2 d-flex flex-column align-items-end gap-1" style="z-index:7;">
                    <span class="badge bg-dark text-white small" id="sizeLabel">{{ __('admin.size') }}:
                        {{ $heroWidth ? $heroWidth . 'px' : 'auto' }} × {{ $heroHeight }}px</span>
                    <span class="badge bg-dark text-white small" id="heightLabel">{{ __('admin.height') }}: {{ $heroHeight }}px</span>
                    <span class="badge bg-dark text-white small" id="zoomLabel">{{ __('admin.zoom') }}: {{ $heroBgSize }}%</span>
                </div>
                <div class="position-absolute top-0 start-0 end-0 p-3" id="miniBannerWrap">
                    @if (!empty($hs?->banner_enabled) && $hs?->banner_text)
                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill"
                            style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15);">
                            <span class="text-gold fw-bold small" id="miniBannerText">{{ $hs->banner_text }}</span>
                            @if ($hs->banner_link)
                                <small class="text-muted" id="miniBannerLink">{{ $hs->banner_link }}</small>
                            @endif
                        </div>
                    @endif
                </div>
                <div id="miniContent" class="position-absolute text-light"
                    style="top: {{ $heroContentPosY }}%; left: {{ $heroContentPosX }}%; max-width:60%; z-index:4;">
                    @if ($showTitle)
                        <h6 class="mb-1" id="miniHeroTitle"
                            style="color: {{ $heroTitleColor }}; {{ $heroTitleSize ? 'font-size:' . $heroTitleSize . 'px;' : '' }}">
                            {{ $heroTitle }}</h6>
                    @endif
                    <p class="small mb-3 {{ $showSubtitle ? '' : 'd-none' }}" id="miniHeroSubtitle"
                        style="white-space: pre-line; color: {{ $heroSubtitleColor }}; {{ $heroSubtitleSize ? 'font-size:' . $heroSubtitleSize . 'px;' : '' }}">
                        {{ $heroSubtitle }}</p>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="#" class="btn btn-sm btn-gold" id="btnPrimaryPreview"
                            style="display: {{ $pVisible ? 'inline-flex' : 'none' }}; background: {{ $pBg }}; border-color: {{ $hs->btn_primary_color ?? '#c7954b' }}; color: {{ $pColor }}; border-radius: {{ $pRadius }}; {{ $heroButtonSize ? 'font-size:' . $heroButtonSize . 'px;' : '' }}">
                            {{ $hs->btn_primary_text ?? __('admin.projects') }}
                        </a>
                        <a href="#" class="btn btn-sm btn-outline-light" id="btnSecondaryPreview"
                            style="display: {{ $sVisible ? 'inline-flex' : 'none' }}; border-color: {{ $hs->btn_secondary_color ?? '#ffffff' }}; color: {{ $sColor }}; background: {{ $sBg }}; border-radius: {{ $sRadius }}; {{ $heroButtonSize ? 'font-size:' . $heroButtonSize . 'px;' : '' }}">
                            {{ $hs->btn_secondary_text ?? __('admin.contact') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" id="miniHeroShow" class="btn btn-sm btn-gold">{{ __('admin.show_preview') }}</button>

        {{-- Tabs --}}
        <ul class="nav nav-tabs mb-3" id="homeSettingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active text-dark fw-semibold" id="hero-tab" data-bs-toggle="tab" data-bs-target="#heroTab" type="button" role="tab">{{ __('admin.header') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark fw-semibold" id="banner-tab" data-bs-toggle="tab" data-bs-target="#bannerTab" type="button" role="tab">{{ __('admin.banner') }}</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link text-dark fw-semibold" id="theme-tab" data-bs-toggle="tab" data-bs-target="#themeTab" type="button" role="tab">{{ __('admin.theme') }}</button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="heroTab" role="tabpanel">
                <div class="card card-dark p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-gold mb-0">{{ __('admin.hero_header') }}</h6>
                        <small class="text-muted">{{ __('admin.media_text_colors_buttons') }}</small>
                    </div>
                    <form method="POST" action="{{ route('admin.home.settings.update') }}" enctype="multipart/form-data"
                        class="row g-3">
                        @csrf
                        <input type="hidden" name="section" value="header">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.hero_title') }}</label>
                            <input type="text" name="hero_title" class="form-control"
                                value="{{ old('hero_title', $settings->hero_title ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.hero_subtitle') }}</label>
                            <textarea name="hero_subtitle" class="form-control" rows="3" placeholder="{{ __('admin.enter_subtitle') }}">{{ old('hero_subtitle', $settings->hero_subtitle ?? '') }}</textarea>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.media_type') }}</label>
                            @php $selType = $settings->hero_media_type ?? ($settings?->hero_video_path ? 'video' : 'image'); @endphp
                            <select name="hero_media_type" class="form-select">
                                <option value="image"
                                    {{ $selType === 'image' ? 'selected' : '' }}>{{ __('admin.image') }}
                                </option>
                                <option value="video"
                                    {{ $selType === 'video' ? 'selected' : '' }}>
                                    {{ __('admin.video_url') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('admin.background_color') }}</label>
                            <input type="color" name="hero_bg_color" class="form-control form-control-color"
                                value="{{ old('hero_bg_color', $settings->hero_bg_color ?? '#0b1220') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="hidden" name="hero_stretch" value="0">
                                <input class="form-check-input" type="checkbox" name="hero_stretch" value="1"
                                    id="heroStretch"
                                    {{ old('hero_stretch', $settings->hero_stretch ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="heroStretch">{{ __('admin.stretch_media') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.title_size') }}</label>
                            <input type="number" name="hero_title_size" class="form-control" min="16"
                                max="96" value="{{ old('hero_title_size', $settings->hero_title_size ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.subtitle_size') }}</label>
                            <input type="number" name="hero_subtitle_size" class="form-control" min="12"
                                max="64"
                                value="{{ old('hero_subtitle_size', $settings->hero_subtitle_size ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.title_color') }}</label>
                            <input type="color" name="hero_title_color" class="form-control form-control-color"
                                value="{{ old('hero_title_color', $settings->hero_title_color ?? '#ffffff') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.subtitle_color') }}</label>
                            <input type="color" name="hero_subtitle_color" class="form-control form-control-color"
                                value="{{ old('hero_subtitle_color', $settings->hero_subtitle_color ?? '#ffffff') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="hidden" name="show_title" value="0">
                                <input class="form-check-input" type="checkbox" name="show_title" value="1"
                                    id="showTitle"
                                    {{ old('show_title', $settings->show_title ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="showTitle">{{ __('admin.show_title') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check">
                                <input type="hidden" name="show_subtitle" value="0">
                                <input class="form-check-input" type="checkbox" name="show_subtitle" value="1"
                                    id="showSubtitle"
                                    {{ old('show_subtitle', $settings->show_subtitle ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="showSubtitle">{{ __('admin.show_subtitle') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.buttons_size') }}</label>
                            <input type="number" name="hero_button_size" class="form-control" min="10"
                                max="48" value="{{ old('hero_button_size', $settings->hero_button_size ?? '') }}">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-4">
                                <input type="hidden" name="overlay_enabled" value="0">
                                <input class="form-check-input" type="checkbox" name="overlay_enabled" value="1"
                                    id="overlayEnabled"
                                    {{ old('overlay_enabled', $settings->overlay_enabled ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="overlayEnabled">{{ __('admin.overlay') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.title_font') }}</label>
                            @php $titleFont = old('hero_title_font', $settings->hero_title_font ?? ''); @endphp
                            <select class="form-select" name="hero_title_font" id="titleFontSelect">
                                <option value="" {{ $titleFont===''?'selected':'' }}>{{ __('admin.default') }}</option>
                                <option value="'Poppins', sans-serif" {{ $titleFont=="'Poppins', sans-serif"?'selected':'' }}>Poppins</option>
                                <option value="'Inter', sans-serif" {{ $titleFont=="'Inter', sans-serif"?'selected':'' }}>Inter</option>
                                <option value="'Montserrat', sans-serif" {{ $titleFont=="'Montserrat', sans-serif"?'selected':'' }}>Montserrat</option>
                                <option value="'Nunito', sans-serif" {{ $titleFont=="'Nunito', sans-serif"?'selected':'' }}>Nunito</option>
                                <option value="'Playfair Display', serif" {{ $titleFont=="'Playfair Display', serif"?'selected':'' }}>Playfair</option>
                                <option value="'Lora', serif" {{ $titleFont=="'Lora', serif"?'selected':'' }}>Lora</option>
                                <option value="'Cairo', sans-serif" {{ $titleFont=="'Cairo', sans-serif"?'selected':'' }}>Cairo</option>
                                <option value="'Tajawal', sans-serif" {{ $titleFont=="'Tajawal', sans-serif"?'selected':'' }}>Tajawal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.subtitle_font') }}</label>
                            @php $subtitleFont = old('hero_subtitle_font', $settings->hero_subtitle_font ?? ''); @endphp
                            <select class="form-select" name="hero_subtitle_font" id="subtitleFontSelect">
                                <option value="" {{ $subtitleFont===''?'selected':'' }}>{{ __('admin.default') }}</option>
                                <option value="'Poppins', sans-serif" {{ $subtitleFont=="'Poppins', sans-serif"?'selected':'' }}>Poppins</option>
                                <option value="'Inter', sans-serif" {{ $subtitleFont=="'Inter', sans-serif"?'selected':'' }}>Inter</option>
                                <option value="'Montserrat', sans-serif" {{ $subtitleFont=="'Montserrat', sans-serif"?'selected':'' }}>Montserrat</option>
                                <option value="'Nunito', sans-serif" {{ $subtitleFont=="'Nunito', sans-serif"?'selected':'' }}>Nunito</option>
                                <option value="'Playfair Display', serif" {{ $subtitleFont=="'Playfair Display', serif"?'selected':'' }}>Playfair</option>
                                <option value="'Lora', serif" {{ $subtitleFont=="'Lora', serif"?'selected':'' }}>Lora</option>
                                <option value="'Cairo', sans-serif" {{ $subtitleFont=="'Cairo', sans-serif"?'selected':'' }}>Cairo</option>
                                <option value="'Tajawal', sans-serif" {{ $subtitleFont=="'Tajawal', sans-serif"?'selected':'' }}>Tajawal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.content_x') }}</label>
                            <input type="number" name="hero_content_pos_x" class="form-control" min="0" max="100"
                                value="{{ old('hero_content_pos_x', $settings->hero_content_pos_x ?? 10) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.content_y') }}</label>
                            <input type="number" name="hero_content_pos_y" class="form-control" min="0" max="100"
                                value="{{ old('hero_content_pos_y', $settings->hero_content_pos_y ?? 20) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('admin.hero_image') }}</label>
                            <input type="file" name="hero_image" class="form-control">
                            @if (!empty($settings?->hero_image_path))
                                <small class="text-muted d-block mt-1">{{ __('admin.current') }}: {{ $settings->hero_image_path }}</small>
                            @endif
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">{{ __('admin.hero_video_url') }}</label>
                            <input type="url" name="hero_video_url" class="form-control"
                                value="{{ old('hero_video_url', $settings->hero_video_url ?? '') }}"
                                placeholder="https://...">
                            @if (!empty($settings?->hero_video_path))
                                <small class="text-muted d-block mt-1">{{ __('admin.uploaded_video') }}
                                    {{ $settings->hero_video_path }}</small>
                            @endif
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">{{ __('admin.upload_mp4') }}</label>
                            <input type="file" name="hero_video_upload" class="form-control"
                                accept="video/mp4,video/webm,video/ogg">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">{{ __('admin.hero_gallery_slider') }}</label>
                            <input type="file" name="hero_gallery[]" class="form-control" multiple>
                            @if (!empty($settings?->hero_gallery))
                                <small class="text-muted d-block mt-1">{{ __('admin.current') }}:
                                    {{ implode(', ', $settings->hero_gallery) }}</small>
                            @endif
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.primary_button_text') }}</label>
                            <input type="text" name="btn_primary_text" class="form-control"
                                value="{{ old('btn_primary_text', $settings->btn_primary_text ?? '') }}"
                                placeholder="{{ __('admin.projects') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.primary_button_link') }}</label>
                            <input type="url" name="btn_primary_link" class="form-control"
                                value="{{ old('btn_primary_link', $settings->btn_primary_link ?? '') }}"
                                placeholder="#projects">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('admin.primary_color') }}</label>
                            <input type="color" name="btn_primary_color" class="form-control form-control-color"
                                value="{{ old('btn_primary_color', $settings->btn_primary_color ?? '#c7954b') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('admin.style') }}</label>
                            @php $pStyle = old('btn_primary_style', $settings->btn_primary_style ?? 'solid'); @endphp
                            <select name="btn_primary_style" class="form-select">
                                <option value="solid" {{ $pStyle === 'solid' ? 'selected' : '' }}>{{ __('admin.solid') }}</option>
                                <option value="outline" {{ $pStyle === 'outline' ? 'selected' : '' }}>{{ __('admin.outline') }}</option>
                                <option value="pill" {{ $pStyle === 'pill' ? 'selected' : '' }}>{{ __('admin.pill') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="btn_primary_visible"
                                    value="1" id="btnPrimaryVisible"
                                    {{ old('btn_primary_visible', $settings->btn_primary_visible ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="btnPrimaryVisible">{{ __('admin.show') }}</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.secondary_button_text') }}</label>
                            <input type="text" name="btn_secondary_text" class="form-control"
                                value="{{ old('btn_secondary_text', $settings->btn_secondary_text ?? '') }}"
                                placeholder="{{ __('admin.contact') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.secondary_button_link') }}</label>
                            <input type="url" name="btn_secondary_link" class="form-control"
                                value="{{ old('btn_secondary_link', $settings->btn_secondary_link ?? '') }}"
                                placeholder="#contact">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('admin.secondary_color') }}</label>
                            <input type="color" name="btn_secondary_color" class="form-control form-control-color"
                                value="{{ old('btn_secondary_color', $settings->btn_secondary_color ?? '#ffffff') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{ __('admin.style') }}</label>
                            @php $sStyle = old('btn_secondary_style', $settings->btn_secondary_style ?? 'outline'); @endphp
                            <select name="btn_secondary_style" class="form-select">
                                <option value="solid" {{ $sStyle === 'solid' ? 'selected' : '' }}>{{ __('admin.solid') }}</option>
                                <option value="outline" {{ $sStyle === 'outline' ? 'selected' : '' }}>{{ __('admin.outline') }}</option>
                                <option value="pill" {{ $sStyle === 'pill' ? 'selected' : '' }}>{{ __('admin.pill') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="btn_secondary_visible"
                                    value="1" id="btnSecondaryVisible"
                                    {{ old('btn_secondary_visible', $settings->btn_secondary_visible ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="btnSecondaryVisible">{{ __('admin.show') }}</label>
                            </div>
                        </div>
                        <div class="col-12 text-start">
                            <button class="btn btn-gold">{{ __('admin.save_header') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="tab-pane fade" id="bannerTab" role="tabpanel">
                <div class="card card-dark p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-gold mb-0">{{ __('admin.banner') }}</h6>
                        <small class="text-muted">{{ __('admin.toggle_and_text_link') }}</small>
                    </div>
                    <form method="POST" action="{{ route('admin.home.settings.update') }}"
                        enctype="multipart/form-data" class="row g-3">
                        @csrf
                        <input type="hidden" name="section" value="banner">
                        <div class="col-12">
                            <div class="d-flex align-items-center gap-3 p-3 rounded border border-secondary bg-dark"
                                id="bannerPreviewCard">
                                <div style="width:96px; height:64px; background:#0f172a; border:1px solid rgba(255,255,255,0.1);"
                                    class="rounded overflow-hidden position-relative">
                                    <img id="bannerPreviewImg"
                                        src="{{ !empty($settings?->banner_image_path) ? asset($settings->banner_image_path) : '' }}"
                                        alt="Preview" style="object-fit:cover; width:100%; height:100%; {{ empty($settings?->banner_image_path) ? 'display:none;' : '' }}">
                                    <div id="bannerPreviewPlaceholder" class="text-center text-muted small"
                                        style="position:absolute; inset:0; display: {{ empty($settings?->banner_image_path) ? 'grid' : 'none' }}; place-items:center;">
                                        {{ __('admin.no_image') }}
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-gold" id="bannerPreviewText">{{ $settings->banner_text ?? 'Banner text' }}
                                    </div>
                                    <div class="text-muted small" id="bannerPreviewLink">{{ $settings->banner_link ?? 'Link' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" name="banner_enabled" value="1"
                                    id="bannerEnabled"
                                    {{ old('banner_enabled', $settings->banner_enabled ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="bannerEnabled">{{ __('admin.show_banner') }}</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.banner_text') }}</label>
                            <input type="text" name="banner_text" class="form-control"
                                value="{{ old('banner_text', $settings->banner_text ?? '') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.banner_link_optional') }}</label>
                            <input type="url" name="banner_link" class="form-control"
                                value="{{ old('banner_link', $settings->banner_link ?? '') }}" placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('admin.banner_image') }}</label> (optional)
                            <input type="file" name="banner_image" class="form-control">
                            @if (!empty($settings?->banner_image_path))
                                <small class="text-muted d-block mt-1">{{ __('admin.current') }}: {{ $settings->banner_image_path }}</small>
                            @endif
                        </div>
                        <div class="col-12 text-start">
                            <button class="btn btn-gold">{{ __('admin.save_banner') }}</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="tab-pane fade" id="themeTab" role="tabpanel">
                <div class="card card-dark p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-gold mb-0">{{ __('admin.theme_site_wide') }}</h6>
                        <small class="text-muted">{{ __('admin.global_colors_buttons') }}</small>
                    </div>
                    <form method="POST" action="{{ route('admin.home.settings.update') }}" class="row g-3">
                        @csrf
                        <input type="hidden" name="section" value="theme">
                        <div class="col-12">
                            <div id="themePreview" class="p-3 rounded border" style="background: {{ $settings->theme_bg ?? '#0b1220' }}; color: {{ $settings->body_text_color ?? '#d4d4d4' }}; border-color: rgba(255,255,255,0.08);">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <h6 id="themePreviewHeading" class="mb-1" style="color: {{ $settings->headings_color ?? '#ffffff' }}">{{ __('admin.preview_heading') }}</h6>
                                        <p id="themePreviewText" class="mb-1 small">{{ __('admin.body_text_example') }}</p>
                                        <a id="themePreviewLink" href="#" class="small" style="color: {{ $settings->link_color ?? '#c7954b' }}">{{ __('admin.link_preview') }}</a>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="button" id="themePreviewBtnPrimary" class="btn btn-sm" style="background: {{ $settings->btn_global_primary_color ?? '#c7954b' }}; border:1px solid {{ $settings->btn_global_primary_color ?? '#c7954b' }}; color:#0f172a;">{{ __('admin.primary') }}</button>
                                        <button type="button" id="themePreviewBtnSecondary" class="btn btn-sm" style="background: transparent; border:1px solid {{ $settings->btn_global_secondary_color ?? '#ffffff' }}; color: {{ $settings->btn_global_secondary_color ?? '#ffffff' }};">{{ __('admin.secondary') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.primary_gold') }}</label>
                            <input type="color" name="theme_primary" class="form-control form-control-color"
                                value="{{ old('theme_primary', $settings->theme_primary ?? '#c7954b') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.dark') }}</label>
                            <input type="color" name="theme_dark" class="form-control form-control-color"
                                value="{{ old('theme_dark', $settings->theme_dark ?? '#0f172a') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.text_color') }}</label>
                            <input type="color" name="theme_text" class="form-control form-control-color"
                                value="{{ old('theme_text', $settings->theme_text ?? '#ffffff') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.background') }}</label>
                            <input type="color" name="theme_bg" class="form-control form-control-color"
                                value="{{ old('theme_bg', $settings->theme_bg ?? '#0b1220') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('admin.headings_color') }}</label>
                            <input type="color" name="headings_color" class="form-control form-control-color"
                                value="{{ old('headings_color', $settings->headings_color ?? '#ffffff') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('admin.body_text') }}</label>
                            <input type="color" name="body_text_color" class="form-control form-control-color"
                                value="{{ old('body_text_color', $settings->body_text_color ?? '#d4d4d4') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">{{ __('admin.link_color') }}</label>
                            <input type="color" name="link_color" class="form-control form-control-color"
                                value="{{ old('link_color', $settings->link_color ?? '#c7954b') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.primary_btn_color') }}</label>
                            <input type="color" name="btn_global_primary_color" class="form-control form-control-color"
                                value="{{ old('btn_global_primary_color', $settings->btn_global_primary_color ?? '#c7954b') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.primary_btn_style') }}</label>
                            @php $gpStyle = old('btn_global_primary_style', $settings->btn_global_primary_style ?? 'solid'); @endphp
                            <select name="btn_global_primary_style" class="form-select">
                                <option value="solid" {{ $gpStyle==='solid'?'selected':'' }}>{{ __('admin.solid') }}</option>
                                <option value="outline" {{ $gpStyle==='outline'?'selected':'' }}>{{ __('admin.outline') }}</option>
                                <option value="pill" {{ $gpStyle==='pill'?'selected':'' }}>{{ __('admin.pill') }}</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.secondary_btn_color') }}</label>
                            <input type="color" name="btn_global_secondary_color" class="form-control form-control-color"
                                value="{{ old('btn_global_secondary_color', $settings->btn_global_secondary_color ?? '#ffffff') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ __('admin.secondary_btn_style') }}</label>
                            @php $gsStyle = old('btn_global_secondary_style', $settings->btn_global_secondary_style ?? 'outline'); @endphp
                            <select name="btn_global_secondary_style" class="form-select">
                                <option value="solid" {{ $gsStyle==='solid'?'selected':'' }}>{{ __('admin.solid') }}</option>
                                <option value="outline" {{ $gsStyle==='outline'?'selected':'' }}>{{ __('admin.outline') }}</option>
                                <option value="pill" {{ $gsStyle==='pill'?'selected':'' }}>{{ __('admin.pill') }}</option>
                            </select>
                        </div>
                        <div class="col-12 text-start">
                            <button class="btn btn-gold">{{ __('admin.save_theme') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const titleInput = document.querySelector('[name="hero_title"]');
            const subtitleInput = document.querySelector('[name="hero_subtitle"]');
            const mediaTypeSelect = document.querySelector('[name="hero_media_type"]');
            const videoInput = document.querySelector('[name="hero_video_url"]');
            const videoUploadInput = document.querySelector('[name="hero_video_upload"]');
            const imageInput = document.querySelector('[name="hero_image"]');
            const galleryInput = document.querySelector('[name="hero_gallery[]"]');
            const heroHeightInput = document.querySelector('[name="hero_height"]');
            const heroWidthInput = document.querySelector('[name="hero_width"]');
            const heroTitleSizeInput = document.querySelector('[name="hero_title_size"]');
            const heroSubtitleSizeInput = document.querySelector('[name="hero_subtitle_size"]');
            const heroButtonSizeInput = document.querySelector('[name="hero_button_size"]');
            const heroContentPosXInput = document.querySelector('[name="hero_content_pos_x"]');
            const heroContentPosYInput = document.querySelector('[name="hero_content_pos_y"]');
            const bannerEnabled = document.querySelector('[name="banner_enabled"]');
            const bannerText = document.querySelector('[name="banner_text"]');
            const bannerLink = document.querySelector('[name="banner_link"]');
            const bannerImageInput = document.querySelector('[name="banner_image"]');
            const overlayEnabledInput = document.getElementById('overlayEnabled');
            const heroBgColorInput = document.querySelector('[name="hero_bg_color"]');
            const heroStretchInput = document.getElementById('heroStretch');
            const sizeLabel = document.getElementById('sizeLabel');
            const heightLabel = document.getElementById('heightLabel');
            const zoomLabel = document.getElementById('zoomLabel');

            const initialVideoSrc =
                heroVideoEl.getAttribute('src') ||
                heroVideoEl.currentSrc ||
                '';
            let currentVideoSrc = initialVideoSrc;

            const playVideo = (src) => {
                const url = src || currentVideoSrc || initialVideoSrc;
                if (!url) return;
                heroVideoEl.pause();
                heroVideoEl.autoplay = true;
                heroVideoEl.muted = true;
                heroVideoEl.loop = true;
                heroVideoEl.playsInline = true;
                heroVideoEl.src = url;
                heroVideoEl.load();
                heroVideoEl.currentTime = 0;
                heroVideoEl.play().catch(() => {});
            };
            heroVideoEl?.addEventListener('loadeddata', () => {
                heroVideoEl.play().catch(() => {});
            });

            // إذا كان النوع Video ولدينا مصدر مبدئي، شغّل مباشرة في المعاينة
            if (mediaTypeSelect?.value === 'video' && (currentVideoSrc || initialVideoSrc)) {
                const startSrc = currentVideoSrc || initialVideoSrc;
                heroVideoEl.src = startSrc;
                heroVideoEl.load();
                heroVideoEl.style.display = 'block';
                heroImgEl.style.display = 'none';
                if (miniHeroCarousel) miniHeroCarousel.style.display = 'none';
                playVideo(startSrc);
            }

            const updateMedia = () => {
                let type = mediaTypeSelect.value;
                // إذا كان لدينا مصدر فيديو (URL أو blob) اعتبره فيديو حتى لو كان السلكت على Image
                if (type !== 'video' && currentVideoSrc) {
                    type = 'video';
                    mediaTypeSelect.value = 'video';
                }
                if (type === 'video') {
                    heroVideoEl.style.display = '';
                    heroImgEl.style.display = 'none';
                    heroImgEl.classList.add('d-none');
                    if (miniHeroCarousel) {
                        miniHeroCarousel.style.display = 'none';
                        miniHeroCarousel.classList.add('d-none');
                    }
                    const candidate = videoInput.value || currentVideoSrc || heroVideoEl.getAttribute('src') || initialVideoSrc;
                    currentVideoSrc = candidate || currentVideoSrc;
                    playVideo(candidate);
                    setTimeout(() => heroVideoEl.play().catch(() => {}), 50);
                } else {
                    heroVideoEl.style.display = 'none';
                    const hasSlider = miniHeroCarouselInner && miniHeroCarouselInner.children.length > 1;
                    if (hasSlider && miniHeroCarousel) {
                        miniHeroCarousel.classList.remove('d-none');
                        miniHeroCarousel.style.display = 'block';
                        heroImgEl.style.display = 'none';
                        heroImgEl.classList.add('d-none');
                    } else {
                        heroImgEl.classList.remove('d-none');
                        heroImgEl.style.display = 'block';
                        if (miniHeroCarousel) {
                            miniHeroCarousel.style.display = 'none';
                            miniHeroCarousel.classList.add('d-none');
                        }
                    }
                }
            };

            titleInput?.addEventListener('input', () => heroTitleEl.textContent = titleInput.value || '{{ addslashes(__('admin.hero_title')) }}');
            subtitleInput?.addEventListener('input', () => {
                heroSubtitleEl.textContent = subtitleInput.value || '{{ addslashes(__('admin.hero_subtitle')) }}';
            });
            mediaTypeSelect?.addEventListener('change', updateMedia);
            videoInput?.addEventListener('input', () => {
                if (videoInput.value) {
                    mediaTypeSelect.value = 'video';
                    currentVideoSrc = videoInput.value;
                    heroVideoEl.src = currentVideoSrc;
                    heroVideoEl.load();
                    playVideo(currentVideoSrc);
                    heroVideoEl.style.display = 'block';
                    heroImgEl.style.display = 'none';
                    if (miniHeroCarousel) miniHeroCarousel.style.display = 'none';
                }
                updateMedia();
            });
            mediaTypeSelect?.addEventListener('change', (e) => {
                if (e.target.value === 'image') {
                    currentVideoSrc = '';
                    heroVideoEl.pause();
                    heroVideoEl.removeAttribute('src');
                    heroVideoEl.load();
                }
                updateMedia();
            });

            imageInput?.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                const url = URL.createObjectURL(file);
                heroImgEl.src = url;
                heroVideoEl.style.display = 'none';
                heroImgEl.style.display = 'block';
                currentVideoSrc = '';
                heroVideoEl.pause();
                heroVideoEl.removeAttribute('src');
                heroVideoEl.load();
                if (miniHeroCarousel) {
                    miniHeroCarousel.style.display = 'none';
                    if (miniHeroCarouselInner) miniHeroCarouselInner.innerHTML = '';
                }
                mediaTypeSelect.value = 'image';
                updateMedia();
            });

            videoUploadInput?.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (!file) return;
                mediaTypeSelect.value = 'video';
                const blob = URL.createObjectURL(file);
                currentVideoSrc = blob;
                heroVideoEl.src = blob;
                heroVideoEl.load();
                heroVideoEl.style.display = 'block';
                heroImgEl.style.display = 'none';
                if (miniHeroCarousel) miniHeroCarousel.style.display = 'none';
                heroOverlayEl?.style.setProperty('display', 'none');
                heroVideoEl.play().catch(() => {});
                updateMedia();
            });

            galleryInput?.addEventListener('change', (e) => {
                const files = Array.from(e.target.files || []);
                if (!files.length) return;
                mediaTypeSelect.value = 'image';
                // single image behaves like hero image
                if (files.length === 1) {
                    const url = URL.createObjectURL(files[0]);
                    heroImgEl.src = url;
                    heroImgEl.style.display = 'block';
                    heroVideoEl.style.display = 'none';
                    currentVideoSrc = '';
                    if (miniHeroCarousel) miniHeroCarousel.style.display = 'none';
                    if (miniHeroCarouselInner) miniHeroCarouselInner.innerHTML = '';
                    updateMedia();
                    return;
                }

                // multiple: rebuild carousel live
                if (!miniHeroCarouselInner || !miniHeroCarousel) {
                    if (!miniHero) return;
                    miniHeroCarousel = document.createElement('div');
                    miniHeroCarousel.id = 'miniHeroCarousel';
                    miniHeroCarousel.className = 'carousel slide w-100 h-100 position-absolute';
                    miniHeroCarousel.dataset.bsRide = 'carousel';
                    miniHeroCarousel.dataset.bsInterval = '4000';
                    miniHeroCarouselInner = document.createElement('div');
                    miniHeroCarouselInner.id = 'miniHeroCarouselInner';
                    miniHeroCarouselInner.className = 'carousel-inner h-100';
                    miniHeroCarousel.appendChild(miniHeroCarouselInner);
                    miniHero.appendChild(miniHeroCarousel);
                }

                miniHeroCarouselInner.innerHTML = '';
                files.forEach((file, idx) => {
                    const slide = document.createElement('div');
                    slide.className = `carousel-item h-100 ${idx === 0 ? 'active' : ''}`;
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = 'w-100 h-100 d-block';
                    img.style.objectFit = heroStretchInput?.checked ? 'fill' : 'contain';
                    img.style.background = heroBgColorInput?.value || '#0b1220';
                    slide.appendChild(img);
                    miniHeroCarouselInner.appendChild(slide);
                });
                applyFit();
                heroImgEl.style.display = 'none';
                heroImgEl.classList.add('d-none');
                heroVideoEl.style.display = 'none';
                if (miniHeroCarousel) {
                    miniHeroCarousel.classList.remove('d-none');
                    miniHeroCarousel.style.display = 'block';
                    if (typeof bootstrap !== 'undefined') {
                        bootstrap.Carousel.getOrCreateInstance(miniHeroCarousel, {
                            interval: 4000,
                            ride: 'carousel'
                        }).to(0);
                    }
                }
                updateMedia();
            });

            const renderBanner = () => {
                if (bannerEnabled?.checked && bannerText?.value) {
                    let html = `<div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill" style="background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.15);">`;
                    html += `<span class="text-gold fw-bold small" id="miniBannerText">${bannerText.value}</span>`;
                    if (bannerLink?.value) {
                        html += `<small class="text-muted" id="miniBannerLink">${bannerLink.value}</small>`;
                    }
                    html += `</div>`;
                    bannerWrap.innerHTML = html;
                } else {
                    bannerWrap.innerHTML = '';
                }
                if (bannerPreviewText) bannerPreviewText.textContent = bannerText?.value || '{{ addslashes(__('admin.banner_text')) }}';
                if (bannerPreviewLink) bannerPreviewLink.textContent = bannerLink?.value || '{{ addslashes(__('admin.link')) }}';
            };
    bannerEnabled?.addEventListener('change', renderBanner);
    bannerText?.addEventListener('input', renderBanner);
    bannerLink?.addEventListener('input', renderBanner);
    bannerImageInput?.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const url = URL.createObjectURL(file);
                    if (bannerPreviewImg) {
                        bannerPreviewImg.src = url;
                        bannerPreviewImg.style.display = 'block';
                    }
                    if (bannerPreviewPlaceholder) bannerPreviewPlaceholder.style.display = 'none';
                }
            });

    titleFontSelect?.addEventListener('change', () => {
        const f = titleFontSelect.value || 'inherit';
        heroTitleEl.style.fontFamily = f;
    });
    subtitleFontSelect?.addEventListener('change', () => {
        const f = subtitleFontSelect.value || 'inherit';
        heroSubtitleEl.style.fontFamily = f;
    });

    const updateThemePreview = () => {
        if (!themePreviewBox) return;
        const primary = themePrimaryInput?.value || '#c7954b';
        const dark = themeDarkInput?.value || '#0f172a';
        const text = themeTextInput?.value || '#ffffff';
        const bg = themeBgInput?.value || '#0b1220';
        const headings = headingsColorInput?.value || text;
        const body = bodyTextColorInput?.value || text;
        const link = linkColorInput?.value || primary;
        const btnPColor = btnGlobalPrimaryColorInput?.value || primary;
        const btnPStyle = btnGlobalPrimaryStyleSelect?.value || 'solid';
        const btnSColor = btnGlobalSecondaryColorInput?.value || '#ffffff';
        const btnSStyle = btnGlobalSecondaryStyleSelect?.value || 'outline';

        themePreviewBox.style.background = bg;
        themePreviewBox.style.color = body;
        if (themePreviewHeading) themePreviewHeading.style.color = headings;
        if (themePreviewText) themePreviewText.style.color = body;
        if (themePreviewLink) themePreviewLink.style.color = link;

        // Primary button preview
        if (themePreviewBtnPrimary) {
            const radius = btnPStyle === 'pill' ? '999px' : '6px';
            const bgP = btnPStyle === 'outline' ? 'transparent' : btnPColor;
            const textP = btnPStyle === 'outline' ? btnPColor : dark;
            themePreviewBtnPrimary.style.background = bgP;
            themePreviewBtnPrimary.style.borderColor = btnPColor;
            themePreviewBtnPrimary.style.color = textP;
            themePreviewBtnPrimary.style.borderRadius = radius;
        }

        // Secondary button preview
        if (themePreviewBtnSecondary) {
            const radius = btnSStyle === 'pill' ? '999px' : '6px';
            let bgS = 'transparent';
            let textS = btnSColor;
            if (btnSStyle === 'solid') {
                bgS = btnSColor;
                textS = dark;
            }
            themePreviewBtnSecondary.style.background = bgS;
            themePreviewBtnSecondary.style.borderColor = btnSColor;
            themePreviewBtnSecondary.style.color = textS;
            themePreviewBtnSecondary.style.borderRadius = radius;
        }
    };

    [
        themePrimaryInput,
        themeDarkInput,
        themeTextInput,
        themeBgInput,
        headingsColorInput,
        bodyTextColorInput,
        linkColorInput,
        btnGlobalPrimaryColorInput,
        btnGlobalPrimaryStyleSelect,
        btnGlobalSecondaryColorInput,
        btnGlobalSecondaryStyleSelect
    ].forEach(el => el?.addEventListener('input', updateThemePreview));

            const applyButtonStyle = (el, style, color, isPrimary) => {
                const radius = style === 'pill' ? '999px' : '6px';
                let bg = color;
                let text = isPrimary ? '#0f172a' : color;
                if (style === 'outline') {
                    bg = 'transparent';
                    text = color;
                }
                if (!isPrimary && style === 'solid') {
                    text = '#0f172a';
                }
                el.style.borderRadius = radius;
                el.style.borderColor = color;
                el.style.background = bg;
                el.style.color = text;
            };

            const refreshButtons = () => {
                btnPrimaryPreview.style.display = btnPrimaryVisible?.checked ? 'inline-flex' : 'none';
                btnSecondaryPreview.style.display = btnSecondaryVisible?.checked ? 'inline-flex' : 'none';
                applyButtonStyle(btnPrimaryPreview, btnPrimaryStyle?.value || 'solid', btnPrimaryColor?.value ||
                    '#c7954b', true);
                applyButtonStyle(btnSecondaryPreview, btnSecondaryStyle?.value || 'outline', btnSecondaryColor
                    ?.value || '#ffffff', false);
            };

            btnPrimaryText?.addEventListener('input', () => btnPrimaryPreview.textContent = btnPrimaryText.value ||
                '{{ addslashes(__('admin.projects')) }}');
            btnSecondaryText?.addEventListener('input', () => btnSecondaryPreview.textContent = btnSecondaryText
                .value || '{{ addslashes(__('admin.contact')) }}');
            btnPrimaryColor?.addEventListener('input', refreshButtons);
            btnSecondaryColor?.addEventListener('input', refreshButtons);
            btnPrimaryStyle?.addEventListener('change', refreshButtons);
            btnSecondaryStyle?.addEventListener('change', refreshButtons);
            btnPrimaryVisible?.addEventListener('change', refreshButtons);
            btnSecondaryVisible?.addEventListener('change', refreshButtons);
            const updateSizeLabel = () => {
                if (!miniHero || !sizeLabel) return;
                const rect = miniHero.getBoundingClientRect();
                sizeLabel.textContent = `{{ __('admin.size') }}: ${Math.round(rect.width)}px × ${Math.round(rect.height)}px`;
            };

            const applyWidth = () => {
                if (!heroWidthInput) return;
                const val = heroWidthInput.value;
                if (val) {
                    miniHero.style.width = `${val}px`;
                    miniHero.style.marginLeft = 'auto';
                    miniHero.style.marginRight = 'auto';
                } else {
                    miniHero.style.width = '100%';
                    miniHero.style.marginLeft = '';
                    miniHero.style.marginRight = '';
                }
                updateSizeLabel();
            };

            heroHeightInput?.addEventListener('input', () => {
                miniHero.style.height = `${heroHeightInput.value || 220}px`;
                if (heightLabel) heightLabel.textContent = `{{ __('admin.height') }}: ${heroHeightInput.value || 220}px`;
                updateSizeLabel();
            });
            heroWidthInput?.addEventListener('input', applyWidth);
            const applyFit = () => {
                const fit = heroStretchInput?.checked ? 'fill' : 'contain';
                heroImgEl.style.objectFit = fit;
                heroVideoEl.style.objectFit = fit;
                document.querySelectorAll('#miniHeroCarousel img').forEach(img => img.style.objectFit = fit);
                if (zoomLabel) zoomLabel.textContent = `{{ __('admin.zoom') }}: ` + (fit === 'fill' ? '{{ __('admin.fill') }}' : '{{ __('admin.contain') }}');
            };
            heroStretchInput?.addEventListener('change', applyFit);
            // X/Y غير مستخدمة في وضع contain
            heroTitleColorInput?.addEventListener('input', () => {
                if (heroTitleEl) heroTitleEl.style.color = heroTitleColorInput.value || '#ffffff';
            });
            heroSubtitleColorInput?.addEventListener('input', () => {
                if (heroSubtitleEl) heroSubtitleEl.style.color = heroSubtitleColorInput.value || '#ffffff';
            });
            const syncVisibility = () => {
                heroTitleEl?.classList.toggle('d-none', showTitleInput ? !showTitleInput.checked : false);
                heroSubtitleEl?.classList.toggle('d-none', showSubtitleInput ? !showSubtitleInput.checked : false);
            };
            syncVisibility();
            showTitleInput?.addEventListener('change', syncVisibility);
            showSubtitleInput?.addEventListener('change', syncVisibility);
            heroTitleSizeInput?.addEventListener('input', () => {
                heroTitleEl.style.fontSize = heroTitleSizeInput.value ? `${heroTitleSizeInput.value}px` : '';
            });
            heroSubtitleSizeInput?.addEventListener('input', () => {
                heroSubtitleEl.style.fontSize = heroSubtitleSizeInput.value ? `${heroSubtitleSizeInput.value}px` : '';
            });
            heroButtonSizeInput?.addEventListener('input', () => {
                const s = heroButtonSizeInput.value ? `${heroButtonSizeInput.value}px` : '';
                btnPrimaryPreview.style.fontSize = s;
                btnSecondaryPreview.style.fontSize = s;
            });
            heroContentPosXInput?.addEventListener('input', () => {
                miniContent.style.left = `${heroContentPosXInput.value || 0}%`;
            });
            heroContentPosYInput?.addEventListener('input', () => {
                miniContent.style.top = `${heroContentPosYInput.value || 0}%`;
            });
            heroBgColorInput?.addEventListener('input', () => {
                const bg = heroBgColorInput.value || '#0b1220';
                miniHero.style.backgroundColor = bg;
                heroImgEl.style.backgroundColor = bg;
                heroVideoEl.style.backgroundColor = bg;
                document.querySelectorAll('#miniHeroCarousel img').forEach(img => img.style.background =
                    bg);
            });

            const updateOverlay = () => {
                if (!heroOverlayEl) return;
                const enabled = overlayEnabledInput?.checked ?? true;
                heroOverlayEl.style.display = enabled ? '' : 'none';
            };
            overlayEnabledInput?.addEventListener('change', updateOverlay);

            updateMedia();
            renderBanner();
            refreshButtons();
            applyWidth();
            applyFit();
            updateOverlay();
            updateSizeLabel();
            window.addEventListener('resize', updateSizeLabel);
            updateThemePreview();
            // mini hero compact float on scroll
            const compactClass = 'miniHero-compact';
            const hiddenClass = 'miniHero-hidden';
            const closeBtn = document.getElementById('miniHeroClose');
            const showBtn = document.getElementById('miniHeroShow');
            let pinTop = false;
            const toggleCompact = () => {
                if (!miniHero) return;
                if (pinTop) {
                    miniHero.classList.remove(hiddenClass);
                    miniHero.classList.remove(compactClass);
                    return;
                }
                const shouldCompact = window.scrollY > 200;
                miniHero.classList.remove(hiddenClass);
                miniHero.classList.toggle(compactClass, shouldCompact);
            };
            window.addEventListener('scroll', toggleCompact);
            closeBtn?.addEventListener('click', () => {
                pinTop = true;
                miniHero?.classList.remove(hiddenClass, compactClass);
                miniHero.style.opacity = '';
                if (showBtn) showBtn.style.display = 'block';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            showBtn?.addEventListener('click', () => {
                pinTop = false;
                toggleCompact();
                if (showBtn) showBtn.style.display = 'none';
            });
            // ابدأ بشكل طبيعي بالأعلى
            miniHero?.classList.remove(compactClass, hiddenClass);
            toggleCompact();
            // تأكيد تشغيل الفيديو الحالي إن وُجد مصدر جاهز
            if (mediaTypeSelect?.value === 'video') {
                playVideo(videoInput?.value || initialVideoSrc);
            }
            const miniHeroCarouselEl = document.getElementById('miniHeroCarousel');
            if (miniHeroCarouselEl && typeof bootstrap !== 'undefined') {
                new bootstrap.Carousel(miniHeroCarouselEl, {
                    interval: 4000,
                    ride: 'carousel'
                });
            }
        });
    </script>
@endpush
