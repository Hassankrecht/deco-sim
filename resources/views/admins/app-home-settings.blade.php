@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 text-gold"><i class="bi bi-phone me-2"></i>App Home Settings</h5>
                <p class="text-muted small mb-0">Flutter app home screen only. Website home settings are separate.</p>
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
            $heroImage = $settings->hero_image_path ? asset($settings->hero_image_path) : null;
            $bannerImage = $settings->banner_image_path ? asset($settings->banner_image_path) : null;
            $gallery = collect($settings->hero_gallery ?? [])->filter();
            $heroImageFit = old('hero_image_fit', $settings->hero_image_fit ?? 'contain');
            $heroObjectFit = [
                'cover' => 'cover',
                'contain' => 'contain',
                'fill' => 'fill',
                'fitWidth' => 'contain',
                'fitHeight' => 'contain',
            ][$heroImageFit] ?? 'contain';
        @endphp

        <div class="card card-dark p-3 mb-4">
            <div class="row g-3 align-items-stretch">
                <div class="col-lg-7">
                    <div class="position-relative rounded-3 overflow-hidden h-100"
                        style="min-height:260px;background:{{ $settings->secondary_color ?? '#111111' }};">
                        @if ($heroImage)
                            <img src="{{ $heroImage }}" class="w-100 h-100 position-absolute top-0 start-0"
                                style="object-fit:{{ $heroObjectFit }};opacity:{{ $settings->hero_image_opacity ?? 1 }};">
                        @elseif ($gallery->isNotEmpty())
                            <img src="{{ asset($gallery->first()) }}" class="w-100 h-100 position-absolute top-0 start-0"
                                style="object-fit:{{ $heroObjectFit }};opacity:{{ $settings->hero_image_opacity ?? 1 }};">
                        @endif
                        @if ($settings->overlay_enabled)
                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                style="background:{{ $settings->overlay_color ?? '#000000' }};opacity:{{ $settings->overlay_opacity ?? 0 }};">
                            </div>
                        @endif
                        <div class="position-relative p-4 d-flex flex-column justify-content-end h-100"
                            style="min-height:260px;color:{{ $settings->text_color ?? '#ffffff' }};">
                            @if ($settings->banner_enabled && $settings->banner_text)
                                <div class="d-inline-flex align-items-center rounded-pill px-3 py-1 mb-3"
                                    style="width:max-content;max-width:100%;background:{{ $settings->primary_color ?? '#d6a84f' }};opacity:{{ $settings->banner_opacity ?? 1 }};">
                                    <span class="small fw-bold text-dark">{{ $settings->banner_text }}</span>
                                </div>
                            @endif
                            <h3 class="fw-bold mb-2">{{ $settings->hero_title ?? 'Deco Sim' }}</h3>
                            <p class="mb-0">{{ $settings->hero_subtitle ?? 'Premium products and services' }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="h-100 rounded-3 border p-3">
                        <h6 class="text-gold mb-3">Current API values</h6>
                        <div class="small text-muted mb-2">Theme mode</div>
                        <div class="mb-3">{{ ucfirst($settings->theme_mode ?? 'auto') }}</div>
                        <div class="small text-muted mb-2">Visible sections</div>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-dark text-gold">Popular: {{ $settings->show_popular_products ? 'on' : 'off' }}</span>
                            <span class="badge bg-dark text-gold">Categories: {{ $settings->show_categories ? 'on' : 'off' }}</span>
                            <span class="badge bg-dark text-gold">Coupons: {{ $settings->show_coupons ? 'on' : 'off' }}</span>
                        </div>
                        <hr>
                        <div class="small text-muted mb-2">Endpoint</div>
                        <code class="small">GET /api/app-home-settings</code>
                    </div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.app-home-settings.update') }}" enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="card card-dark p-3 h-100">
                        <h6 class="text-gold mb-3">Hero</h6>
                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="hero_title" class="form-control"
                                value="{{ old('hero_title', $settings->hero_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subtitle</label>
                            <textarea name="hero_subtitle" class="form-control" rows="3">{{ old('hero_subtitle', $settings->hero_subtitle) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Media type</label>
                            <select name="hero_media_type" class="form-select">
                                @foreach (['image' => 'Image', 'video' => 'Video', 'gallery' => 'Gallery'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('hero_media_type', $settings->hero_media_type) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hero image fit</label>
                            <select name="hero_image_fit" class="form-select">
                                @foreach ([
                                    'contain' => 'Contain - show full image, no crop',
                                    'cover' => 'Cover - fill box, may crop',
                                    'fill' => 'Fill - stretch to box',
                                    'fitWidth' => 'Fit width',
                                    'fitHeight' => 'Fit height',
                                ] as $value => $label)
                                    <option value="{{ $value }}" {{ $heroImageFit === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hero image</label>
                            <input type="file" name="hero_image" class="form-control" accept="image/*">
                            @if ($heroImage)
                                <div class="small text-muted mt-1">Current: {{ $settings->hero_image_path }}</div>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Hero video URL</label>
                            <input type="url" name="hero_video_url" class="form-control"
                                value="{{ old('hero_video_url', $settings->hero_video_url) }}">
                        </div>
                        <div>
                            <label class="form-label">Hero gallery</label>
                            <input type="file" name="hero_gallery[]" class="form-control" accept="image/*" multiple>
                            @if ($gallery->isNotEmpty())
                                <div class="small text-muted mt-1">Current gallery images: {{ $gallery->count() }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card card-dark p-3 h-100">
                        <h6 class="text-gold mb-3">Banner</h6>
                        <div class="form-check mb-3">
                            <input type="hidden" name="banner_enabled" value="0">
                            <input class="form-check-input" type="checkbox" name="banner_enabled" value="1"
                                id="bannerEnabled" {{ old('banner_enabled', $settings->banner_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label" for="bannerEnabled">Show banner</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Banner text</label>
                            <input type="text" name="banner_text" class="form-control"
                                value="{{ old('banner_text', $settings->banner_text) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Banner link</label>
                            <input type="text" name="banner_link" class="form-control"
                                value="{{ old('banner_link', $settings->banner_link) }}">
                        </div>
                        <div>
                            <label class="form-label">Banner image</label>
                            <input type="file" name="banner_image" class="form-control" accept="image/*">
                            @if ($bannerImage)
                                <div class="small text-muted mt-1">Current: {{ $settings->banner_image_path }}</div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card card-dark p-3 h-100">
                        <h6 class="text-gold mb-3">Theme</h6>
                        <div class="mb-3">
                            <label class="form-label">Mode</label>
                            <select name="theme_mode" class="form-select">
                                @foreach (['auto' => 'Auto', 'light' => 'Light', 'dark' => 'Dark'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('theme_mode', $settings->theme_mode) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-3">
                            @foreach ([
                                'primary_color' => 'Primary color',
                                'secondary_color' => 'Secondary color',
                                'button_color' => 'Button color',
                                'text_color' => 'Text color',
                                'overlay_color' => 'Overlay color',
                            ] as $field => $label)
                                <div class="col-sm-6">
                                    <label class="form-label">{{ $label }}</label>
                                    <input type="color" name="{{ $field }}" class="form-control form-control-color"
                                        value="{{ old($field, $settings->{$field} ?? '#000000') }}">
                                </div>
                            @endforeach
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-sm-6">
                                <label class="form-label">Font family</label>
                                <input type="text" name="font_family" class="form-control"
                                    value="{{ old('font_family', $settings->font_family) }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Font size</label>
                                <input type="number" name="font_size" class="form-control" min="8" max="40"
                                    value="{{ old('font_size', $settings->font_size) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card card-dark p-3 h-100">
                        <h6 class="text-gold mb-3">Opacity and Sections</h6>
                        <div class="form-check mb-3">
                            <input type="hidden" name="overlay_enabled" value="0">
                            <input class="form-check-input" type="checkbox" name="overlay_enabled" value="1"
                                id="overlayEnabled" {{ old('overlay_enabled', $settings->overlay_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label" for="overlayEnabled">Enable overlay</label>
                        </div>
                        <div class="row g-3">
                            @foreach ([
                                'overlay_opacity' => 'Overlay opacity',
                                'banner_opacity' => 'Banner opacity',
                                'hero_image_opacity' => 'Hero image opacity',
                            ] as $field => $label)
                                <div class="col-sm-4">
                                    <label class="form-label">{{ $label }}</label>
                                    <input type="number" name="{{ $field }}" class="form-control"
                                        min="0" max="1" step="0.05"
                                        value="{{ old($field, $settings->{$field}) }}">
                                </div>
                            @endforeach
                        </div>
                        <hr>
                        @foreach ([
                            'show_popular_products' => 'Show popular products',
                            'show_categories' => 'Show categories',
                            'show_coupons' => 'Show coupons',
                        ] as $field => $label)
                            <div class="form-check mb-2">
                                <input type="hidden" name="{{ $field }}" value="0">
                                <input class="form-check-input" type="checkbox" name="{{ $field }}" value="1"
                                    id="{{ $field }}" {{ old($field, $settings->{$field}) ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-gold px-4">Save App Settings</button>
            </div>
        </form>
    </div>
@endsection
