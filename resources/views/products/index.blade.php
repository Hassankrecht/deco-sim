@extends('layouts.app')

@section('title', __('messages.products.hero_title'))
@section('meta_description', __('messages.meta.products_description'))

@section('content')
    <!-- Hero Section -->
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 12_10_16 PM.png') }}" alt="{{ __('messages.products.hero_title') }}"
            class="akg-hero-img" loading="lazy">



        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.products.hero_title') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ __('messages.nav.products') }}</li>
            </ol>
        </div>
    </div>
  
        @php
            $activeCategory = $categoryId ?? request('category');
        @endphp

    <div class="container-xxl py-5">
        <div class="text-center mb-4">
            <h5 class="akg-section-label">{{ __('messages.home.products_label') }}</h5>
            <h2 class="akg-section-head">{{ __('messages.products.browse') }}</h2>
        </div>

        @if(isset($parentCategories) && $parentCategories->count())
            @php
                $activeParent = $parentCategories->first()->id ?? null;
                foreach ($parentCategories as $parent) {
                    if ($parent->children->contains(fn($c) => (string)$c->id === (string)($activeCategory ?? ''))) {
                        $activeParent = $parent->id;
                        break;
                    }
                }
            @endphp
            <div class="akg-newcard mb-4 p-3 text-center akg-cat-nav">
                <ul class="nav nav-pills justify-content-center mb-3 flex-wrap gap-2" id="prodParentNav">
                    @foreach($parentCategories as $parent)
                        <li class="nav-item">
                            <a class="nav-link {{ $activeParent === $parent->id ? 'active' : '' }}" href="#"
                               data-parent="{{ $parent->id }}"
                               onclick="event.preventDefault(); document.querySelectorAll('.prod-child').forEach(el => el.classList.add('d-none')); document.getElementById('prod-child-{{ $parent->id }}').classList.remove('d-none'); document.querySelectorAll('#prodParentNav .nav-link').forEach(l=>l.classList.remove('active')); this.classList.add('active');">
                                {{ $parent->name_localized }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                @foreach($parentCategories as $parent)
                    <ul class="nav nav-pills justify-content-center flex-wrap gap-2 prod-child {{ $activeParent === $parent->id ? '' : 'd-none' }}" id="prod-child-{{ $parent->id }}">
                        @foreach($parent->children as $child)
                            <li class="nav-item">
                                <a class="nav-link {{ (string) $activeCategory === (string) $child->id ? 'active' : '' }}"
                                   href="{{ route('products.index', ['category' => $child->id]) }}">
                                    {{ $child->name_localized }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endforeach
            </div>
        @endif

            <div class="row g-4">

            @forelse ($products as $item)
                <div class="col-md-3">

                    <div class="akg-card h-100">

                        {{-- IMAGE --}}
                        @php
                            $resolvePath = function ($path) {
                                if (!$path) {
                                    return null;
                                }
                                return (str_starts_with($path, 'public/') || str_starts_with($path, 'assets/'))
                                    ? asset($path)
                                    : asset('storage/' . $path);
                            };

                            $img = $resolvePath($item->image)
                                ?? ($item->images->first()
                                    ? $resolvePath($item->images->first()->image)
                                    : asset('assets/img/default.jpg'));
                        @endphp

                        <img src="{{ $img }}" class="card-img-top" style="height: 200px; object-fit: cover;"
                            alt="{{ $item->title }}" loading="lazy">

                        <div class="card-body">

                            <h5 class="text-gold fw-bold">{{ $item->title_localized }}</h5>

                            <p class="small text-muted mb-1">
                                {{ __('messages.products.category_label') }}: {{ $item->category->name_localized ?? __('messages.common.na') }}
                            </p>

                            <p class="fw-bold text-gold">{{ \App\Support\Currency::format($item->price) }}</p>

                            <a href="{{ route('products.show', $item->id) }}" class="btn btn-gold w-100 fw-bold">
                                {{ __('messages.products.view_details') }}
                            </a>

                        </div>

                    </div>

                </div>
            @empty
                <div class="col-12">
                    <div class="akg-card text-center py-5">
                        <h5 class="text-gold mb-2">{{ __('messages.products.no_products') }}</h5>
                        <p class="text-muted mb-3">{{ __('messages.products.no_products_hint') }}</p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a class="btn btn-gold" href="{{ route('products.index') }}">{{ __('messages.products.all') }}</a>
                            <a class="btn btn-outline-gold" href="{{ route('projects.index') }}">{{ __('messages.products.see_all_projects') }}</a>
                        </div>
                    </div>
                </div>
            @endforelse

        </div>

        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
@endsection
