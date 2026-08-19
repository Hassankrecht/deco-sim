@extends('layouts.admin')

@section('content')
    <div class="container py-5">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold" style="color: #c7954b;">
                    ✏️ {{ __('admin.edit_product') }}
                </h3>
                <p class="text-muted small mb-0">{{ $product->title }}</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-gold fw-semibold px-4">
                ← {{ __('admin.back') }}
            </a>
        </div>

        <div class="card card-dark">

            <div class="card-body p-4">

                {{-- Success Message --}}
                @if (session('success'))
                    <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
                @endif

                {{-- EDIT FORM --}}
                <form action="{{ route('admin.products.update', $product->id) }}" 
                      method="POST" enctype="multipart/form-data">

                    @csrf
                    @method('PUT')

                    {{-- Category --}}
                    <div class="mb-3">
                        <div class="card border mb-2" style="background: #f8f9fa;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0 fw-bold" style="color: #c7954b;">{{ __('admin.product_category') }}</h6>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addParentCatModal">{{ __('admin.add_parent') }}</button>
                                        <button type="button" class="btn btn-outline-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addChildCatModal">{{ __('admin.add_child') }}</button>
                                    </div>
                                </div>
                                <label class="form-label fw-semibold">{{ __('admin.select_category') }}</label>
                                <select name="category_id" class="form-select" required id="categorySelect">
                                    @php
                                        $parents = $categories->whereNull('parent_id');
                                    @endphp
                                    @foreach ($parents as $parent)
                                        <option disabled style="font-weight: bold;">— {{ $parent->name }} —</option>
                                        @foreach ($parent->children->where('parent_id', $parent->id)->unique('id') as $child)
                                            <option value="{{ $child->id }}" {{ $product->category_id == $child->id ? 'selected' : '' }}>
                                                &nbsp;&nbsp;{{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ __('admin.select_child_category_help') }}</small>
                            </div>
                        </div>
                    </div>

                    {{-- Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('admin.title_portuguese') }}</label>
                        <input type="text" name="title" 
                               class="form-control" 
                               value="{{ $product->title }}" required>
                    </div>

                    {{-- Description --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('admin.description_portuguese') }}</label>
                        <textarea name="description" rows="4" class="form-control">{{ $product->description }}</textarea>
                    </div>

                    {{-- English (optional) --}}
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #c7954b;">{{ __('admin.english_optional') }}</h5>
                        @php
                            $locales = config('app.admin_content_locales', ['pt', 'en']);
                            $primaryLocale = config('app.admin_primary_content_locale', 'pt');
                        @endphp
                        @foreach ($locales as $locale)
                            @continue($locale === $primaryLocale)
                            @php $t = $product->translations->firstWhere('locale', $locale); @endphp
                            <div class="border rounded p-3 mb-3 bg-white">
                                <div class="mb-2">
                                    <label class="form-label small mb-1">{{ $locale === 'pt' ? __('admin.title_portuguese') : __('admin.title_english') }}</label>
                                    <input type="text" name="translations[{{ $locale }}][title]" class="form-control"
                                        value="{{ old('translations.' . $locale . '.title', $t->title ?? '') }}">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small mb-1">{{ $locale === 'pt' ? __('admin.description_portuguese') : __('admin.description_english') }}</label>
                                    <textarea name="translations[{{ $locale }}][description]" rows="3" class="form-control">{{ old('translations.' . $locale . '.description', $t->description ?? '') }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    {{-- Price --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('admin.price') }}</label>
                        <input type="number" name="price" step="0.01" 
                               class="form-control"
                               value="{{ $product->price }}" required>
                    </div>

                    {{-- Standard Set Components --}}
                    <div class="mb-4">
                        <h5 class="fw-bold mb-3" style="color: #c7954b;">{{ __('admin.standard_set_components') }}</h5>
                        <div id="components-list">
                            @php
                                $components = old('components', $product->productComponents->toArray());
                            @endphp
                            @foreach ($components as $i => $component)
                                <div class="component-row mb-3 p-3 border rounded bg-white">
                                    <div class="mb-2">
                                        <label class="form-label small mb-1">{{ __('admin.name_portuguese') }}</label>
                                        <input type="text" name="components[{{ $i }}][name_translations][pt]" value="{{ $component['name_translations']['pt'] ?? '' }}" class="form-control" required>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label small mb-1">{{ __('admin.name_english') }} <span class="text-muted">({{ __('admin.optional') }})</span></label>
                                        <input type="text" name="components[{{ $i }}][name_translations][en]" value="{{ $component['name_translations']['en'] ?? '' }}" class="form-control">
                                    </div>
                                    @if(isset($component['name_translations']['ar']) && !empty($component['name_translations']['ar']))
                                        <input type="hidden" name="components[{{ $i }}][name_translations][ar]" value="{{ $component['name_translations']['ar'] }}">
                                    @endif
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <input type="text" name="components[{{ $i }}][width]" value="{{ $component['width'] ?? '' }}" placeholder="{{ __('admin.width') }}" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="components[{{ $i }}][length]" value="{{ $component['length'] ?? '' }}" placeholder="{{ __('admin.length') }}" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="components[{{ $i }}][height]" value="{{ $component['height'] ?? '' }}" placeholder="{{ __('admin.height') }}" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" name="components[{{ $i }}][material]" value="{{ $component['material'] ?? '' }}" placeholder="{{ __('admin.material') }}" class="form-control">
                                        </div>
                                    </div>
                                    <div class="mt-2 text-end">
                                        <button type="button" class="btn btn-danger btn-sm btn-remove-component">{{ __('admin.remove') }}</button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" id="add-component" class="btn btn-secondary mt-2">{{ __('admin.add_component') }}</button>
                    </div>

                    {{-- Main Image --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('admin.main_image') }}</label><br>

                        @php
                            $mainImg = $product->image
                                ? (str_starts_with($product->image, 'public/') || str_starts_with($product->image, 'assets/')
                                    ? asset($product->image)
                                    : asset('storage/' . $product->image))
                                : null;
                        @endphp

                        @if ($mainImg)
                            <img src="{{ $mainImg }}"
                                 class="img-thumbnail mb-3"
                                 style="max-width:200px;">
                        @endif

                        <input type="file" name="image" class="form-control mt-2">
                    </div>

                    {{-- SUBMIT --}}
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-gold fw-semibold px-4">
                            🔄 {{ __('admin.update_product') }}
                        </button>

                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-dark px-4">
                            {{ __('admin.cancel') }}
                        </a>
                    </div>

                </form>

                {{-- DELETE MAIN IMAGE --}}
                @if ($product->image)
                    <form action="{{ route('admin.products.mainimage.delete', $product->id) }}"
                          method="POST" 
                          class="mt-3"
                          onsubmit="return confirm('{{ __('admin.delete_only_main_image') }}')">

                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger btn-sm">
                            🗑️ {{ __('admin.delete') }} {{ __('admin.main_image') }}
                        </button>
                    </form>
                @endif

                {{-- GALLERY SECTION --}}
                <h4 class="fw-bold mt-5">{{ __('admin.gallery_images') }}</h4>

                <div class="d-flex flex-wrap gap-3">

                    @foreach ($product->images as $img)
                        @php
                            $gallerySrc = $img->image
                                ? (str_starts_with($img->image, 'public/') || str_starts_with($img->image, 'assets/')
                                    ? asset($img->image)
                                    : asset('storage/' . $img->image))
                                : null;
                        @endphp
                        <div class="position-relative">

                            @if ($gallerySrc)
                            <img src="{{ $gallerySrc }}"
                                 style="width:120px; height:90px; object-fit:cover;"
                                 class="rounded shadow-sm">
                            @endif

                            {{-- Delete one gallery image --}}
                            <form action="{{ route('admin.products.image.delete', $img->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('{{ __('admin.delete_this_image') }}')"
                                  class="position-absolute top-0 end-0 m-1">

                                @csrf
                                @method('DELETE')

                                <button class="btn btn-sm btn-danger p-1">
                                    <i class="bi bi-x-lg"></i>
                                </button>

                            </form>

                        </div>
                    @endforeach

                </div>

                {{-- ADD MORE GALLERY IMAGES --}}
                <form action="{{ route('admin.products.addImages', $product->id) }}"
                      method="POST"
                      class="mt-4"
                      enctype="multipart/form-data">

                    @csrf

                    <label class="form-label fw-semibold">{{ __('admin.add_more_images') }}</label>
                    <input type="file" name="gallery[]" class="form-control" multiple>

                    <button class="btn btn-dark btn-sm mt-2">
                        ➕ {{ __('admin.upload_images') }}
                    </button>

                </form>

            </div>
        </div>

    </div>
@endsection

@push('modals')
{{-- ADD PARENT CATEGORY MODAL --}}
<div class="modal fade" id="addParentCatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.add_parent_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">{{ __('admin.parent_categories_organize') }}</p>
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.name_portuguese') }}</label>
                    <input type="text" id="parentCatName" class="form-control" placeholder="{{ __('admin.placeholder_parent_category') }}">
                </div>
                <div class="row g-2">
                    @foreach(config('app.admin_content_locales', ['pt', 'en']) as $locale)
                        @continue($locale === config('app.admin_primary_content_locale', 'pt'))
                        <div class="col-12">
                            <label class="form-label">{{ __('admin.name_english') }} <span class="text-muted">({{ __('admin.optional') }})</span></label>
                            <input type="text" id="parentCatName_{{ $locale }}" class="form-control">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-gold" onclick="saveParentCategory()">{{ __('admin.save_category') }}</button>
            </div>
        </div>
    </div>
</div>

{{-- ADD CHILD CATEGORY MODAL --}}
<div class="modal fade" id="addChildCatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('admin.add_child_category') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.parent_category') }}</label>
                    <select id="childCatParent" class="form-select">
                        <option value="">{{ __('admin.select_parent') }}</option>
                        @php $parents = $categories->whereNull('parent_id'); @endphp
                        @foreach($parents as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.name_portuguese') }}</label>
                    <input type="text" id="childCatName" class="form-control" placeholder="{{ __('admin.placeholder_child_category') }}">
                </div>
                <div class="row g-2">
                    @foreach(config('app.admin_content_locales', ['pt', 'en']) as $locale)
                        @continue($locale === config('app.admin_primary_content_locale', 'pt'))
                        <div class="col-12">
                            <label class="form-label">{{ __('admin.name_english') }} <span class="text-muted">({{ __('admin.optional') }})</span></label>
                            <input type="text" id="childCatName_{{ $locale }}" class="form-control">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin.cancel') }}</button>
                <button type="button" class="btn btn-gold" onclick="saveChildCategory()">{{ __('admin.save_category') }}</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
// Dynamic add/remove for product components (Portuguese primary, English optional)
document.getElementById('add-component').onclick = function() {
    var list = document.getElementById('components-list');
    var index = list.children.length;
    var row = document.createElement('div');
    row.className = 'component-row mb-3 p-3 border rounded bg-white';
    row.innerHTML = `
        <div class="mb-2">
            <label class="form-label small mb-1">{{ __('admin.name_portuguese') }}</label>
            <input type="text" name="components[${index}][name_translations][pt]" class="form-control" required>
        </div>
        <div class="mb-2">
            <label class="form-label small mb-1">{{ __('admin.name_english') }} <span class="text-muted">({{ __('admin.optional') }})</span></label>
            <input type="text" name="components[${index}][name_translations][en]" class="form-control">
        </div>
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="components[${index}][width]" placeholder="{{ __('admin.width') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="text" name="components[${index}][length]" placeholder="{{ __('admin.length') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="text" name="components[${index}][height]" placeholder="{{ __('admin.height') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="text" name="components[${index}][material]" placeholder="{{ __('admin.material') }}" class="form-control">
            </div>
        </div>
        <div class="mt-2 text-end">
            <button type="button" class="btn btn-danger btn-sm btn-remove-component">{{ __('admin.remove') }}</button>
        </div>
    `;
    list.appendChild(row);
};
document.addEventListener('click', function(e) {
    if(e.target.classList.contains('btn-remove-component')) {
        e.target.parentElement.parentElement.remove();
    }
});
</script>
@endpush
