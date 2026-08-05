@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="card card-dark p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1 text-gold"><i class="bi bi-tags me-2"></i>Categories</h5>
                    <p class="text-muted small mb-0">Manage product categories</p>
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $primaryLocale = config('app.admin_primary_content_locale', 'pt');
                $locales = config('app.admin_content_locales', ['pt', 'en']);
                $parentOptions = $categories->whereNull('parent_id');
            @endphp

            <div class="akg-newcard p-3 mb-4">
                <h6 class="text-gold fw-bold mb-3">Add Category</h6>
                <form action="{{ route('admin.categories.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Name ({{ $primaryLocale === 'pt' ? 'Português' : strtoupper($primaryLocale) }})</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Parent</label>
                        <select name="parent_id" class="form-select">
                            <option value="">No parent</option>
                            @foreach ($parentOptions as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name_localized }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Order</label>
                        <input type="number" name="order" class="form-control" value="0">
                    </div>

                    @foreach ($locales as $locale)
                        @if ($locale === $primaryLocale) @continue @endif
                        <div class="col-md-12">
                            <label class="form-label small mb-1">Name ({{ $locale === 'en' ? 'English' : strtoupper($locale) }})</label>
                            <input type="text" name="translations[{{ $locale }}][name]" class="form-control">
                        </div>
                    @endforeach

                    <div class="col-12">
                        <button class="btn btn-gold">Create</button>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-dark table-striped align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width: 70px;">#</th>
                            <th>Name</th>
                            <th>Parent</th>
                            <th style="width: 100px;">Order</th>
                            <th style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name_localized }}</td>
                                <td>{{ $category->parent?->name_localized ?? '—' }}</td>
                                <td>{{ $category->order ?? 0 }}</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-gold" data-bs-toggle="modal" data-bs-target="#editCategory{{ $category->id }}">Edit</button>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @foreach ($categories as $category)
        <div class="modal fade" id="editCategory{{ $category->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content card-dark">
                    <form action="{{ route('admin.categories.update', $category->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Category</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Name ({{ $primaryLocale === 'pt' ? 'Português' : strtoupper($primaryLocale) }})</label>
                                <input type="text" name="name" class="form-control" value="{{ $category->name }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Parent</label>
                                <select name="parent_id" class="form-select">
                                    <option value="">No parent</option>
                                    @foreach ($parentOptions as $parent)
                                        <option value="{{ $parent->id }}" {{ $category->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->name_localized }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Order</label>
                                <input type="number" name="order" class="form-control" value="{{ $category->order ?? 0 }}">
                            </div>

                            @foreach ($locales as $locale)
                                @if ($locale === $primaryLocale) @continue @endif
                                @php $tr = $category->translations->firstWhere('locale', $locale); @endphp
                                <div class="mb-3">
                                    <label class="form-label small mb-1">Name ({{ $locale === 'en' ? 'English' : strtoupper($locale) }})</label>
                                    <input type="text" name="translations[{{ $locale }}][name]" class="form-control" value="{{ $tr->name ?? '' }}">
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button class="btn btn-warning">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection
