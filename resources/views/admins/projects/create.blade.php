@extends('layouts.admin')

@section('content')
    <div class="container py-5">

        {{-- HEADER --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold" style="color: #c7954b;">
                    ➕ {{ __('admin.add_new_project') }}
                </h3>
                <p class="text-muted small mb-0">{{ __('admin.create_project_subtitle') }}</p>
            </div>

            <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-gold fw-semibold px-4">
                ← {{ __('admin.back') }}
            </a>
        </div>

        {{-- ERRORS --}}
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm">
                <strong>{{ __('admin.whoops') }}</strong> {{ __('admin.fix_issue') }}
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        {{-- CARD FORM --}}
        <div class="card card-dark">
            <div class="card-body p-4">

                <form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-4">

                        {{-- TITLE --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #c7954b;">{{ __('admin.title_portuguese') }}</label>
                            <input type="text" name="title" value="{{ old('title') }}"
                                class="form-control" required>
                        </div>

                        {{-- LOCATION --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" style="color: #c7954b;">{{ __('admin.location') }}</label>
                            <input type="text" name="location" value="{{ old('location') }}"
                                class="form-control">
                        </div>

                        {{-- CATEGORIES --}}
                        <div class="col-12">
                            <div class="card border mb-3" style="background: #f8f9fa;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0 fw-bold" style="color: #c7954b;">{{ __('admin.project_categories') }}</h6>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addParentCatModal">{{ __('admin.add_parent') }}</button>
                                            <button type="button" class="btn btn-outline-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addChildCatModal">{{ __('admin.add_child') }}</button>
                                        </div>
                                    </div>
                                    <label class="form-label fw-semibold">{{ __('admin.select_categories') }}</label>
                                    <select name="categories[]" class="form-select" multiple size="5" id="categoriesSelect">
                                @php
                                    $parents = $categories->whereNull('parent_id');
                                @endphp
                                @foreach($parents as $cat)
                                    <option disabled>— {{ $cat->name }} —</option>
                                    @foreach($cat->children->where('parent_id', $cat->id)->unique('id') as $child)
                                        <option value="{{ $child->id }}" {{ collect(old('categories', []))->contains($child->id) ? 'selected' : '' }}>
                                            &nbsp;&nbsp;{{ $child->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            <small class="text-muted">{{ __('admin.select_categories_help') }}</small>
                                </div>
                            </div>
                        </div>

                        {{-- DESCRIPTION --}}
                        <div class="col-12">
                        <label class="form-label fw-semibold" style="color: #c7954b;">{{ __('admin.description_portuguese') }}</label>
                        <textarea name="description" rows="4"
                            class="form-control">{{ old('description') }}</textarea>
                    </div>

                    {{-- TRANSLATIONS --}}
                    <div class="col-12">
                        <div class="card border" style="background: #f8f9fa;">
                            <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color: #c7954b;">{{ __('admin.english_optional') }}</h6>
                            @php
                                $locales = config('app.admin_content_locales', ['pt', 'en']);
                                $primaryLocale = config('app.admin_primary_content_locale', 'pt');
                            @endphp
                            @foreach ($locales as $locale)
                                @continue($locale === $primaryLocale)
                                <div class="border rounded p-3 mb-3 bg-white">
                                    <div class="mb-2">
                                        <label class="form-label small mb-1">{{ $locale === 'pt' ? __('admin.title_portuguese') : __('admin.title_english') }}</label>
                                        <input type="text" name="translations[{{ $locale }}][title]" class="form-control" value="{{ old('translations.'.$locale.'.title') }}">
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label small mb-1">{{ $locale === 'pt' ? __('admin.description_portuguese') : __('admin.description_english') }}</label>
                                        <textarea name="translations[{{ $locale }}][description]" rows="3" class="form-control">{{ old('translations.'.$locale.'.description') }}</textarea>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>

                        {{-- STATUS --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #c7954b;">{{ __('admin.status') }}</label>
                            <select name="status" class="form-select">
                                <option value="1">{{ __('admin.active') }}</option>
                                <option value="2">{{ __('admin.pending') }}</option>
                                <option value="3">{{ __('admin.completed') }}</option>
                            </select>
                        </div>

                        {{-- DATE --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #c7954b;">{{ __('admin.date') }}</label>
                            <input type="date" name="date" value="{{ old('date') }}"
                                class="form-control">
                        </div>

                        {{-- MAIN IMAGE --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold" style="color: #c7954b;">{{ __('admin.main_image') }}</label>
                            <input type="file" name="main_image"
                                class="form-control" accept="image/*">
                        </div>

                        {{-- GALLERY --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="color: #c7954b;">{{ __('admin.gallery_images_multiple') }}</label>
                            <input type="file" name="gallery[]" multiple
                                class="form-control" accept="image/*">
                            <small class="text-muted">{{ __('admin.upload_several_images') }}</small>
                        </div>

                    </div>

                    {{-- SUBMIT --}}
                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-gold fw-semibold px-5">
                            ✔ {{ __('admin.save_project') }}
                        </button>
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-dark px-4">
                            {{ __('admin.cancel') }}
                        </a>
                    </div>

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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">{{ __('admin.parent_categories_available') }}</p>
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.name_portuguese') }}</label>
                    <input type="text" id="parentCatName" class="form-control" placeholder="{{ __('admin.placeholder_parent_category') }}">
                </div>
                <div class="row g-2">
                    @foreach(config('app.admin_content_locales', ['pt', 'en']) as $locale)
                        @continue($locale === config('app.admin_primary_content_locale', 'pt'))
                        <div class="col-12">
                            <label class="form-label">{{ $locale === 'pt' ? __('admin.name_portuguese') : __('admin.name_english') }} <span class="text-muted">({{ __('admin.optional') }})</span></label>
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
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <label class="form-label">{{ $locale === 'pt' ? __('admin.name_portuguese') : __('admin.name_english') }} <span class="text-muted">({{ __('admin.optional') }})</span></label>
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
function saveParentCategory() {
    const name = document.getElementById('parentCatName').value;
    if (!name) {
        alert('{{ __('admin.please_enter_category_name') }}');
        return;
    }
    
    const translations = {};
    @foreach(config('app.admin_content_locales', ['pt', 'en']) as $locale)
        @continue($locale === config('app.admin_primary_content_locale', 'pt'))
        translations['{{ $locale }}'] = {
            name: document.getElementById('parentCatName_{{ $locale }}').value || name
        };
    @endforeach
    
    fetch('{{ route('admin.projects.categories.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name, translations })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('{{ __('admin.failed_to_save_category') }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __('admin.failed_to_save_category') }}');
    });
}

function saveChildCategory() {
    const parentId = document.getElementById('childCatParent').value;
    const name = document.getElementById('childCatName').value;
    
    if (!parentId || !name) {
        alert('{{ __('admin.please_select_parent_and_name') }}');
        return;
    }
    
    const translations = {};
    @foreach(config('app.admin_content_locales', ['pt', 'en']) as $locale)
        @continue($locale === config('app.admin_primary_content_locale', 'pt'))
        translations['{{ $locale }}'] = {
            name: document.getElementById('childCatName_{{ $locale }}').value || name
        };
    @endforeach
    
    fetch('{{ route('admin.projects.categories.store') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name, parent_id: parentId, translations })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('{{ __('admin.failed_to_save_category') }}');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('{{ __('admin.failed_to_save_category') }}');
    });
}
</script>
@endpush
