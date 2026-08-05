@extends('layouts.admin')

@section('content')
    @push('head')
        <style>
            .category-pill {
                padding: 4px 10px;
                border-radius: 999px;
                font-size: 0.85rem;
                border: 1px solid #1f2937;
                background: #f8f9fb;
                color: #1f2937 !important;
            }
            .category-pill.active {
                background: linear-gradient(90deg, #c7954b, #d8aa65);
                color: #0f172a !important;
                border-color: #1f2937;
            }
            .category-pill i { color: #c7954b; }
            .count-badge {
                background: #0f172a;
                color: #f8f9fb;
                border-radius: 999px;
                padding: 2px 8px;
                font-size: 0.75rem;
                font-weight: 700;
            }
            .pill-actions .btn {
                padding: 2px 6px;
            }
            .parent-nav-box {
                border-bottom: 1px solid #d1d5db;
                padding-bottom: 10px;
                margin-bottom: 12px;
            }
        </style>
    @endpush

    <div class="container py-4">

        <div class="card card-dark p-3">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1 text-gold"><i class="bi bi-box-seam me-2"></i>Products Management</h5>
                    <p class="text-muted small mb-0">Manage products and categories</p>
                </div>
                <div class="d-flex gap-2">
                    {{-- ADD PRODUCT --}}
                    <a href="{{ route('admin.products.create') }}" class="btn btn-gold fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> New Product
                    </a>
                </div>
            </div>

            <div>

                {{-- SUCCESS MESSAGE --}}
                @if (session('success'))
                    <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
                @endif

                {{-- FILTER BAR --}}
                <div class="card card-dark p-3 mb-3">
                    <form class="row g-2 align-items-end" method="GET" action="{{ route('admin.products.index') }}">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Search</label>
                            <input type="text" name="q" class="form-control" placeholder="Title or description" value="{{ $search ?? '' }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Sort</label>
                            <select name="sort" class="form-select">
                                <option value="newest" {{ ($sort ?? '')==='newest' ? 'selected' : '' }}>Newest</option>
                                <option value="oldest" {{ ($sort ?? '')==='oldest' ? 'selected' : '' }}>Oldest</option>
                                <option value="price_asc" {{ ($sort ?? '')==='price_asc' ? 'selected' : '' }}>Price low → high</option>
                                <option value="price_desc" {{ ($sort ?? '')==='price_desc' ? 'selected' : '' }}>Price high → low</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small mb-1">Category</label>
                            <select name="category" class="form-select">
                                <option value="">All</option>
                                @foreach($allCategories as $cat)
                                    <option value="{{ $cat->id }}" {{ ($categoryId ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button class="btn btn-gold w-100">Apply</button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-outline-dark w-100">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4 col-12">
                        <div class="card card-dark p-3 h-100 d-flex justify-content-center">
                            <div class="text-muted small">Products</div>
                            <div class="fs-4 fw-bold text-gold">{{ $productsTotal ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="card card-dark p-3 h-100 d-flex justify-content-center">
                            <div class="text-muted small">Parent categories</div>
                            <div class="fs-4 fw-bold text-gold">{{ $parentCount ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="card card-dark p-3 h-100 d-flex justify-content-center">
                            <div class="text-muted small">Child categories</div>
                            <div class="fs-4 fw-bold text-gold">{{ $childCount ?? 0 }}</div>
                        </div>
                    </div>
                </div>

                @php
                    $activeParent = $parentCategories->first()->id ?? null;
                    foreach ($parentCategories as $parent) {
                        if ($parent->children->contains(fn($c) => (string)$c->id === (string)($categoryId ?? ''))) {
                            $activeParent = $parent->id;
                            break;
                        }
                    }
                    $activeChildId = $categoryId; // null means "All"
                @endphp

                <div class="akg-newcard mb-4 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-gold fw-bold mb-0">Categories</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addParentModal">Parent Category</button>
                            <button class="btn btn-outline-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addChildModal">Child Category</button>
                        </div>
                    </div>

                    @if($parentCategories->count())

                    {{-- Parents nav --}}
                    <div class="parent-nav-box">
                        <ul class="nav nav-pills justify-content-start flex-wrap gap-2 mb-0" id="parentNav">
                            @foreach($parentCategories as $parent)
                                <li class="nav-item">
                                    <div class="nav-link category-pill {{ $activeParent === $parent->id ? 'active' : '' }} d-flex align-items-center gap-2 text-dark" data-parent="{{ $parent->id }}">
                                        <a href="#" class="text-decoration-none text-dark fw-semibold" onclick="event.preventDefault(); switchParent({{ $parent->id }});">{{ $parent->name_localized }}</a>
                                        <div class="dropdown ms-auto">
                                            <button class="btn btn-sm btn-outline-gold py-0 px-1 dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-dark">
                                                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editParentModal{{ $parent->id }}">Edit</button></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.categories.destroy', $parent->id) }}" method="POST" onsubmit="return confirm('Delete this category?')" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Children nav --}}
                    @foreach($parentCategories as $parent)
                        <div class="child-bar {{ $activeParent === $parent->id ? '' : 'd-none' }}" id="children-{{ $parent->id }}">
                            @php
                                $childTotal = $parent->children->pluck('id')->sum(fn($id) => $productCounts[$id] ?? 0);
                            @endphp
                            <ul class="nav nav-pills justify-content-start flex-wrap gap-2">
                                <li class="nav-item">
                                    <div class="nav-link category-pill d-flex align-items-center gap-2 {{ $activeChildId ? '' : 'active' }} text-dark">
                                        <a href="{{ route('admin.products.index') }}" class="text-decoration-none text-dark fw-semibold">All</a>
                                        <span class="count-badge">{{ $childTotal }}</span>
                                    </div>
                                </li>
                                @foreach($parent->children as $child)
                                    <li class="nav-item">
                                        <div class="nav-link category-pill d-flex align-items-center gap-2 {{ (string)$categoryId === (string)$child->id ? 'active' : '' }} text-dark">
                                        <a href="{{ route('admin.products.category', $child->id) }}" class="text-decoration-none text-dark fw-semibold">
                                            {{ $child->name_localized }}
                                        </a>
                                        <span class="count-badge">{{ $productCounts[$child->id] ?? 0 }}</span>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-gold py-0 px-1 dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-dark">
                                                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editChildModal{{ $child->id }}">Edit</button></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('admin.categories.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Delete this category?')" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endforeach
                    @else
                        <div class="alert alert-info mb-0">No categories yet. Add a parent category first.</div>
                    @endif
                </div>

                {{-- ========== ADD PARENT MODAL ========== --}}
                <div class="modal fade" id="addParentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content card-dark">
                            <form action="{{ route('admin.categories.store') }}" method="POST">
                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">Add Parent Category</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Name (Português)</label>
                                        <input type="text" name="name" class="form-control" placeholder="Category name" required>
                                    </div>
                                    @php
                                        $locales = config('app.admin_content_locales', ['pt', 'en']);
                                        $primaryLocale = config('app.admin_primary_content_locale', 'pt');
                                    @endphp
                                    @foreach ($locales as $locale)
                                        @if ($locale === $primaryLocale) @continue @endif
                                        <div class="mb-3">
                                            <label class="form-label small mb-1">Name ({{ $locale === 'en' ? 'English' : strtoupper($locale) }})</label>
                                            <input type="text" name="translations[{{ $locale }}][name]" class="form-control">
                                        </div>
                                    @endforeach
                                    <div class="mt-3">
                                        <label class="form-label">Order (optional)</label>
                                        <input type="number" name="order" class="form-control" value="0">
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button class="btn btn-warning">Add</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ========== ADD CHILD MODAL ========== --}}
                <div class="modal fade" id="addChildModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content card-dark">
                            <form action="{{ route('admin.categories.store') }}" method="POST">
                                @csrf

                                <div class="modal-header">
                                    <h5 class="modal-title">Add Child Category</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Parent</label>
                                        <select name="parent_id" class="form-select" required>
                                            @foreach($parentCategories as $parent)
                                                <option value="{{ $parent->id }}">{{ $parent->name_localized }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Name (Português)</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>
                                    @php
                                        $locales = config('app.admin_content_locales', ['pt', 'en']);
                                        $primaryLocale = config('app.admin_primary_content_locale', 'pt');
                                    @endphp
                                    @foreach ($locales as $locale)
                                        @if ($locale === $primaryLocale) @continue @endif
                                        <div class="mb-3">
                                            <label class="form-label small mb-1">Name ({{ $locale === 'en' ? 'English' : strtoupper($locale) }})</label>
                                            <input type="text" name="translations[{{ $locale }}][name]" class="form-control">
                                        </div>
                                    @endforeach
                                    <div class="mt-3">
                                        <label class="form-label">Order (optional)</label>
                                        <input type="number" name="order" class="form-control" value="0">
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button class="btn btn-warning">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- ========== EDIT PARENT MODALS ========== --}}
                @foreach ($parentCategories as $parent)
                    <div class="modal fade" id="editParentModal{{ $parent->id }}" tabindex="-1">
                                <div class="modal-dialog">
                            <div class="modal-content card-dark">
                                <form action="{{ route('admin.categories.update', $parent->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Parent</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name (Português)</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ $parent->name }}" required>
                                        </div>
                                        @php
                                            $locales = config('app.admin_content_locales', ['pt', 'en']);
                                            $primaryLocale = config('app.admin_primary_content_locale', 'pt');
                                        @endphp
                                        @foreach ($locales as $locale)
                                            @if ($locale === $primaryLocale) @continue @endif
                                            @php $tr = $parent->translations->firstWhere('locale',$locale); @endphp
                                            <div class="mb-3">
                                                <label class="form-label small mb-1">Name ({{ $locale === 'en' ? 'English' : strtoupper($locale) }})</label>
                                                <input type="text" name="translations[{{ $locale }}][name]" class="form-control" value="{{ $tr->name ?? '' }}">
                                            </div>
                                        @endforeach
                                        <div class="mt-3">
                                            <label class="form-label">Order</label>
                                            <input type="number" name="order" class="form-control" value="{{ $parent->order ?? 0 }}">
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button class="btn btn-warning">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- ========== EDIT CHILD MODALS ========== --}}
                @foreach ($parentCategories as $parent)
                    @foreach ($parent->children as $child)
                    <div class="modal fade" id="editChildModal{{ $child->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content card-dark">
                                    <form action="{{ route('admin.categories.update', $child->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Child</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Parent</label>
                                                <select name="parent_id" class="form-select" required>
                                                    @foreach($parentCategories as $p)
                                                        <option value="{{ $p->id }}" {{ $child->parent_id == $p->id ? 'selected' : '' }}>{{ $p->name_localized }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Name (Português)</label>
                                                <input type="text" name="name" class="form-control" value="{{ $child->name }}" required>
                                            </div>
                                            @php
                                                $locales = config('app.admin_content_locales', ['pt', 'en']);
                                                $primaryLocale = config('app.admin_primary_content_locale', 'pt');
                                            @endphp
                                            @foreach ($locales as $locale)
                                                @if ($locale === $primaryLocale) @continue @endif
                                                @php $tr = $child->translations->firstWhere('locale',$locale); @endphp
                                                <div class="mb-3">
                                                    <label class="form-label small mb-1">Name ({{ $locale === 'en' ? 'English' : strtoupper($locale) }})</label>
                                                    <input type="text" name="translations[{{ $locale }}][name]" class="form-control" value="{{ $tr->name ?? '' }}">
                                                </div>
                                            @endforeach
                                            <div class="mt-3">
                                                <label class="form-label">Order</label>
                                                <input type="number" name="order" class="form-control" value="{{ $child->order ?? 0 }}">
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button class="btn btn-warning">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endforeach



                {{-- ===================== PRODUCTS TABLE ===================== --}}
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Main Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Created</th>
                            <th>Price</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($products as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                {{-- MAIN IMAGE --}}
                                <td>
                                    @php
                                        $resolvePath = function ($path) {
                                            if (!$path) {
                                                return null;
                                            }
                                            if (str_starts_with($path, 'public/') || str_starts_with($path, 'assets/')) {
                                                return asset($path);
                                            }
                                            if (file_exists(public_path('storage/' . $path))) {
                                                return asset('storage/' . $path);
                                            }
                                            return null;
                                        };

                                        $src = $resolvePath($item->image) ?? asset('assets/img/default.jpg');
                                    @endphp

                                    <img src="{{ $src }}" class="rounded shadow-sm"
                                        style="width:80px; height:60px; object-fit:cover;">
                                </td>

                                <td class="fw-semibold">{{ $item->title }}</td>

                                <td>{{ $item->category->name ?? '—' }}</td>

                                <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</td>

                                <td>{{ \App\Support\Currency::format($item->price) }}</td>

                                <td>
                                    <div class="d-flex gap-2">

                                        <a href="{{ route('admin.products.edit', $item->id) }}"
                                            class="btn btn-sm btn-outline-gold fw-semibold px-3">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $item->id) }}" method="POST"
                                            onsubmit="return confirm('Delete this product?')" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger fw-semibold px-3">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $products->links() }}
                </div>

            </div>
        </div>
    </div>
    <script>
    function switchParent(id) {
        document.querySelectorAll('.child-bar').forEach(el => el.classList.add('d-none'));
        const target = document.getElementById('children-' + id);
        if (target) target.classList.remove('d-none');
        document.querySelectorAll('#parentNav .nav-link').forEach(link => link.classList.remove('active'));
        const active = document.querySelector(`#parentNav .nav-link[data-parent="${id}"]`);
        if (active) active.classList.add('active');
    }
    </script>

@endsection
