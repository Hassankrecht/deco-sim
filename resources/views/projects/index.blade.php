@extends('layouts.app')

@section('title', __('messages.projects_page.hero_title'))
@section('meta_description', 'Discover our portfolio of luxury carpentry and interior design projects completed by Deco Sim in the UAE.')

@section('content')
    <div class=" akg-hero-img-box">
        <img src="{{ asset('assets/img/ChatGPT Image Nov 7, 2025, 11_59_33 AM.png') }}" alt="{{ __('messages.projects_page.hero_title') }}"
            class="akg-hero-img" loading="lazy">



        <div class="container text-center hero-content">
            <h1 class="akg-hero-title text-gold mb-3">{{ __('messages.projects_page.hero_title') }}</h1>

            <ol class="breadcrumb justify-content-center text-uppercase {{ app()->getLocale() === 'ar' ? 'flex-row-reverse' : '' }}">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('messages.nav.home') }}</a></li>
                <li class="breadcrumb-item text-light active">{{ __('messages.nav.projects') }}</li>
            </ol>
        </div>
    </div>

    <div class="container-xxl py-5">
        <div class="container">
            <h2 class="akg-section-head mb-2">{{ __('messages.projects_page.section_title') }}</h2>
            <p class="text-muted mb-4">{{ __('messages.projects_page.section_sub') }}</p>

            @if(isset($categories) && $categories->count())
                @php
                    $activeParent = $categories->first()->id ?? null;
                    $activeChildSlug = null;
                    foreach ($categories as $parent) {
                        if ($parent->slug === ($categorySlug ?? null)) {
                            $activeParent = $parent->id;
                            $activeChildSlug = null;
                            break;
                        }
                        if ($parent->children->contains(fn($c) => $c->slug === ($categorySlug ?? null))) {
                            $activeParent = $parent->id;
                            $activeChildSlug = $categorySlug;
                            break;
                        }
                    }
                @endphp
                <div class="akg-newcard mb-4 p-3 text-center akg-cat-nav">
                    <ul class="nav nav-pills justify-content-center mb-3 flex-wrap gap-2" id="parentNav">
                        @foreach($categories as $parent)
                            <li class="nav-item">
                                <a class="nav-link {{ $activeParent === $parent->id ? 'active' : '' }}" href="#"
                                   data-parent="{{ $parent->id }}"
                                   onclick="event.preventDefault(); document.querySelectorAll('.child-group').forEach(el => el.classList.add('d-none')); document.getElementById('child-{{ $parent->id }}').classList.remove('d-none'); document.querySelectorAll('#parentNav .nav-link').forEach(l=>l.classList.remove('active')); this.classList.add('active');">
                                    {{ $parent->name_localized }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @foreach($categories as $parent)
                        <div id="child-{{ $parent->id }}" class="child-group {{ $activeParent === $parent->id ? '' : 'd-none' }}">
                            <ul class="nav nav-pills justify-content-center flex-wrap gap-2">
                                <li class="nav-item">
                                    <a class="nav-link {{ ($categorySlug ?? null) === $parent->slug ? 'active' : '' }}"
                                       href="{{ route('projects.index', ['category' => $parent->slug]) }}">
                                        {{ __('All') ?? 'All' }}
                                    </a>
                                </li>
                                @foreach($parent->children as $child)
                                    <li class="nav-item">
                                        <a class="nav-link {{ ($categorySlug ?? '') === $child->slug ? 'active' : '' }}"
                                           href="{{ route('projects.index', ['category' => $child->slug]) }}">
                                            {{ $child->name_localized }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="row g-4">
                @forelse($projects as $project)
                    <div class="col-lg-4 col-md-6">
                        <div class="akg-project-card h-100">

                            <!-- IMAGE -->
                            @php
                                $resolvePath = function ($path) {
                                    if (!$path) return null;
                                    // تصحيح المسارات القديمة من storage/public/assets إلى assets
                                    if (str_contains($path, 'storage/public/assets/')) {
                                        $path = str_replace('storage/public/', '', $path);
                                    }
                                    return (str_starts_with($path, 'public/') || str_starts_with($path, 'assets/'))
                                        ? asset($path)
                                        : asset('storage/' . $path);
                                };
                                $mainImg = $resolvePath($project->main_image ?? null) ?? ($project->main_image_url ?? asset('assets/img/default.jpg'));
                                $gallery = collect($project->gallery_urls ?? [])
                                    ->filter()
                                    ->values();
                                if ($gallery->isEmpty()) {
                                    $gallery = $project->images->pluck('image_path')->map(fn($p) => $resolvePath($p))->filter()->values();
                                }
                            @endphp
                            <img src="{{ $mainImg }}" class="akg-project-img"
                                alt="{{ $project->title }}" loading="lazy">

                            <!-- CONTENT -->
                            <div class="text-center mt-3 px-3 pb-3">
                                <h5 class="text-gold fw-bold">{{ $project->title_localized }}</h5>

                                <p class="text-muted small">
                                    {{ Str::limit($project->description_localized, 90) }}
                                </p>

                                <button class="btn btn-gold-small view-project-btn" data-id="{{ $project->id }}"
                                    data-title="{{ e($project->title_localized) }}"
                                    data-description="{{ e($project->description_localized) }}"
                                    data-image="{{ $mainImg }}"
                                    data-url="{{ route('projects.show', $project->id) }}"
                                    data-gallery='@json($gallery)'>
                                    <i class="bi bi-eye me-2"></i> {{ __('messages.projects_page.view_details') }}
                                </button>
                            </div>

                        </div>
                    </div>

                @empty
                    <p class="text-center text-light">{{ __('messages.projects.no_projects') }}</p>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $projects->links() }}
            </div>
        </div>
    </div>

    {{-- ✅ نافذة المشروع المنبثقة --}}
    <!-- 🖼️ Modal for Lightbox -->
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



    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const modal = new bootstrap.Modal(document.getElementById('projectModal'));
            const modalTitle = document.getElementById('projectTitle');
            const modalDesc = document.getElementById('projectDescription');
            const galleryContainer = document.getElementById('lightboxGallery');

            // Lightbox overlay element
            const lightbox = document.createElement('div');
            lightbox.id = 'lightboxOverlay';
            lightbox.style.display = 'none';
            lightbox.innerHTML = `
        <div class="lightbox-content">
            <img id="lightboxImage" src="" alt="">
            <button class="lightbox-prev">&#10094;</button>
            <button class="lightbox-next">&#10095;</button>
            <span class="lightbox-close">&times;</span>
        </div> `;
            document.body.appendChild(lightbox);

            const lightboxImage = lightbox.querySelector('#lightboxImage');
            const prevBtn = lightbox.querySelector('.lightbox-prev');
            const nextBtn = lightbox.querySelector('.lightbox-next');
            const closeBtn = lightbox.querySelector('.lightbox-close');
            let currentIndex = 0,
                allImages = [];

            // Open project modal
            document.querySelectorAll('.view-project-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const title = this.dataset.title;
                    const desc = this.dataset.description;
                    const mainImage = this.dataset.image;
                    const gallery = JSON.parse(this.dataset.gallery || '[]');
                    modalTitle.textContent = title;
                    modalDesc.textContent = desc;
                    galleryContainer.innerHTML = '';

                    // Combine main image + gallery
                    allImages = [mainImage, ...gallery].filter(Boolean);

                    if (!allImages.length) {
                        const empty = document.createElement('p');
                        empty.classList.add('text-muted', 'small', 'w-100', 'text-center');
                        empty.textContent = @json(__('messages.projects.no_gallery'));
                        galleryContainer.appendChild(empty);
                    } else {
                        allImages.forEach((src, index) => {
                            const img = document.createElement('img');
                            img.src = src;
                            img.alt = `${title} - ${index}`;
                            img.loading = 'lazy';
                            img.classList.add('lightbox-thumb', 'rounded', 'shadow');
                            img.style.width = '180px';
                            img.style.height = '130px';
                            img.style.objectFit = 'cover';
                            img.style.cursor = 'pointer';
                            img.addEventListener('click', () => openLightbox(index));
                            galleryContainer.appendChild(img);
                        });
                    }

                    modal.show();
                });
            });

            // Lightbox controls
            function openLightbox(index) {
                currentIndex = index;
                lightboxImage.src = allImages[currentIndex];
                lightboxImage.alt = modalTitle.textContent || 'Project image';
                lightbox.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                lightbox.style.display = 'none';
                document.body.style.overflow = '';
            }

            function showNext() {
                currentIndex = (currentIndex + 1) % allImages.length;
                lightboxImage.src = allImages[currentIndex];
            }

            function showPrev() {
                currentIndex = (currentIndex - 1 + allImages.length) % allImages.length;
                lightboxImage.src = allImages[currentIndex];
            }

            // Buttons
            nextBtn.onclick = showNext;
            prevBtn.onclick = showPrev;
            closeBtn.onclick = closeLightbox;
            lightbox.onclick = (e) => {
                if (e.target === lightbox) closeLightbox();
            };

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (lightbox.style.display === 'flex') {
                    if (e.key === 'ArrowRight') showNext();
                    if (e.key === 'ArrowLeft') showPrev();
                    if (e.key === 'Escape') closeLightbox();
                }
            });
        });
    </script>
@endsection
