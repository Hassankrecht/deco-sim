@extends('layouts.app')

@section('title', ($product->title_localized ?? $product->title) . ' | ' . __('messages.products.hero_title'))
@section('meta_description', __('messages.meta.product_show_description', ['product' => $product->title_localized ?? $product->title]))

@section('content')
    <div class="akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_10_16 PM.png') }}" alt="{{ __('messages.product_show.breadcrumb') }}"
            class="akg-hero-img" loading="lazy">

        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.product_show.breadcrumb') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products.index') }}">{{ __('messages.nav.products') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ $product->title_localized ?? $product->title }}</li>
            </ol>
        </div>
    </div>
  
    @php
        $resolvePath = function ($path) {
            if (!$path) {
                return null;
            }
            return (str_starts_with($path, 'public/') || str_starts_with($path, 'assets/'))
                ? asset($path)
                : asset('storage/' . $path);
        };

        $mainImage = $resolvePath($product->image)
            ?? ($product->images->first()
                ? $resolvePath($product->images->first()->image)
                : asset('assets/img/default.jpg'));
    @endphp

    <div class="container-xxl py-5">
        <div class="row g-5">

            {{-- LEFT GALLERY --}}
            <div class="col-lg-6">

                <img id="mainImage" src="{{ $mainImage }}" class="img-fluid rounded shadow mb-3"
                    style="max-height:450px; object-fit:cover;" alt="{{ $product->title }}" loading="lazy">

                <div class="d-flex flex-wrap gap-2">
                    <img src="{{ $mainImage }}" onclick="document.getElementById('mainImage').src=this.src"
                        class="rounded shadow" style="width:90px;height:70px;object-fit:cover;cursor:pointer;"
                        alt="{{ $product->title }}" loading="lazy">
                    @foreach ($product->images as $img)
                        @php $thumb = $resolvePath($img->image); @endphp
                        @if ($thumb)
                        <img src="{{ $thumb }}"
                            onclick="document.getElementById('mainImage').src=this.src" class="rounded shadow"
                            style="width:90px;height:70px;object-fit:cover;cursor:pointer;"
                            alt="{{ $product->title }} thumbnail" loading="lazy">
                        @endif
                    @endforeach
                </div>

            </div>

            {{-- RIGHT INFO --}}
            <div class="col-lg-6">

                <h2 class="text-gold fw-bold d-flex align-items-center gap-2 mb-2">
                    {{ $product->title_localized }}
                    {{-- BADGES: Custom Made, 3–7 Days Delivery, Lebanese Craftsmanship --}}
                    <span class="badge bg-primary text-light ms-1 badge-sm">{{ __('messages.products.badge_custom_made') }}</span>
                    <span class="badge bg-success text-light ms-1 badge-sm"><i class="fa fa-truck me-1"></i> {{ __('messages.products.badge_delivery') }}</span>
                    <span class="badge bg-warning text-dark ms-1 badge-sm"><i class="fa fa-star me-1"></i> {{ __('messages.products.badge_craftsmanship') }}</span>
                </h2>

                {{-- TRUST SIGNALS --}}
                <div class="mb-2">
                    <span class="text-success fw-semibold me-3"><i class="fa fa-check-circle me-1"></i> {{ __('messages.products.trust_handcrafted') }}</span>
                    <span class="text-info fw-semibold me-3"><i class="fa fa-shield-alt me-1"></i> {{ __('messages.products.trust_guarantee') }}</span>
                    <span class="text-warning fw-semibold"><i class="fa fa-award me-1"></i> {{ __('messages.products.trust_materials') }}</span>
                </div>

                <h4 class="text-light mb-1">{{ __('messages.product_show.starting_from') ?? 'Starting from' }} {{ \App\Support\Currency::format($product->price) }}</h4>
                <p class="text-muted mb-3">{{ __('messages.product_show.price_note') ?? 'Price applies to the displayed standard size and materials.' }}</p>

                <p class="text-muted">{{ $product->description_localized }}</p>

                {{-- ADD TO CART (STANDARD SIZE ONLY) --}}
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit"
                            class="btn btn-gold text-dark fw-semibold px-4 mt-3"
                            data-track-action="buy_click"
                            data-track-meta='@json(["product_id" => $product->id, "source" => "product_show"])'>
                        <i class="fa fa-cart-plus me-1"></i> {{ __('messages.product_show.add_to_cart_standard') ?? 'Add to Cart (Standard Size)' }}
                    </button>
                </form>

                {{-- CUSTOM SIZE CTA (redirects to contact) --}}
                <a href="{{ route('contact') }}" class="btn btn-outline-gold text-light fw-semibold px-4 ms-2 mt-3">
                    <i class="fa fa-ruler-combined me-1"></i> {{ __('messages.products.request_custom_size') }}
                </a>
                <div class="small text-muted mt-1">{{ __('Custom sizes & finishes available on request. Contact us for a quote.') }}</div>
                <div class="small text-muted mt-1">{{ __('messages.products.custom_sizes_note') }}</div>

                {{-- DELIVERY & PRODUCTION TIME SECTION --}}
                <div class="mt-4">
                    <h6 class="fw-bold mb-1"><i class="fa fa-clock me-1"></i> {{ __('messages.products.delivery_time_title') }}</h6>
                    <div class="text-muted">{{ __('messages.products.delivery_time_note') }}</div>
                </div>

                <hr class="my-4 border-gold">

                {{-- SPECIFICATIONS --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.specs') }}</h4>

                <ul class="list-group mb-4">
                    <li class="list-group-item bg-dark text-light border-secondary">
                        <strong>{{ __('messages.product_show.category_label') }}:</strong> {{ $product->category->name_localized ?? __('messages.common.na') }}
                    </li>
                    <li class="list-group-item bg-dark text-light border-secondary">
                        <strong>{{ __('messages.product_show.sku_label') }}:</strong> {{ $product->id }}
                    </li>
                    <li class="list-group-item bg-dark text-light border-secondary">
                        <strong>{{ __('messages.product_show.price_label') }}:</strong> {{ \App\Support\Currency::format($product->price) }}
                    </li>
                </ul>

                {{-- STANDARD SET COMPONENTS & DIMENSIONS --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.standard_set_components') ?? 'Standard Set Components & Dimensions' }}</h4>
                @if($product->productComponents && $product->productComponents->count())
                <div class="table-responsive mb-2">
                    <table class="table table-dark table-bordered align-middle mb-0">
                        <thead class="table-secondary text-dark">
                            <tr>
                                <th>{{ __('messages.product_show.component_name') ?? 'Component' }}</th>
                                <th>{{ __('messages.product_show.width_label') ?? 'Width' }}</th>
                                <th>{{ __('messages.product_show.length_label') ?? 'Length' }}</th>
                                <th>{{ __('messages.product_show.height_label') ?? 'Height' }}</th>
                                <th>{{ __('messages.product_show.material_label') ?? 'Material' }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($product->productComponents as $component)
                                <tr>
                                    <td>{{ $component->name_translated }}</td>
                                    <td>{{ $component->width }}</td>
                                    <td>{{ $component->length }}</td>
                                    <td>{{ $component->height }}</td>
                                    <td>{{ $component->material }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                    <p class="text-muted">{{ __('messages.product_show.no_components') ?? 'No standard components defined for this product.' }}</p>
                @endif
                <p class="text-muted mb-4 small">{{ __('messages.product_show.components_disclaimer') ?? 'All dimensions apply to the base price. Custom sizes and layouts are available upon request.' }}</p>

                {{-- FEATURES --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.features') }}</h4>

                <ul class="text-light mb-4">
                    <li>✔ {{ __('messages.product_show.feature_1') }}</li>
                    <li>✔ {{ __('messages.product_show.feature_2') }}</li>
                    <li>✔ {{ __('messages.product_show.feature_3') }}</li>
                    <li>✔ {{ __('messages.product_show.feature_4') }}</li>
                </ul>

                {{-- SUITABLE FOR --}}
                <h4 class="text-gold fw-bold mb-3">{{ __('messages.product_show.best_for') }}</h4>

                <p class="text-muted">
                    {{ __('messages.product_show.best_for_desc') }}
                </p>

            </div>

        </div>

    </div>
@endsection
