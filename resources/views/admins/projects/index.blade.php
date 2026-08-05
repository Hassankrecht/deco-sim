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
            .pill-actions .btn { padding: 2px 6px; }
            .parent-nav-box { border-bottom: 1px solid #d1d5db; padding-bottom: 10px; margin-bottom: 12px; }
        </style>
    @endpush

    <div class="container py-4">

        <div class="card card-dark p-3">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-1 text-gold"><i class="bi bi-kanban me-2"></i>Projects Management</h5>
                    <p class="text-muted small mb-0">Manage projects and categories</p>
                </div>
                <div>
                    <a href="{{ route('admin.projects.create') }}" class="btn btn-gold fw-semibold">
                        <i class="bi bi-plus-lg me-1"></i> New Project
                    </a>
                </div>
            </div>

            {{-- Summary --}}
            <div class="row g-3 mb-3">
                <div class="col-md-3 col-6">
                    <div class="card card-dark p-3 h-100">
                        <div class="text-muted small">Projects</div>
                        <div class="fs-4 fw-bold text-gold">{{ $totalProjects ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card card-dark p-3 h-100">
                        <div class="text-muted small">Active</div>
                        <div class="fs-4 fw-bold text-success">{{ $statusCounts[1] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card card-dark p-3 h-100">
                        <div class="text-muted small">Pending</div>
                        <div class="fs-4 fw-bold text-warning">{{ $statusCounts[2] ?? 0 }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="card card-dark p-3 h-100">
                        <div class="text-muted small">Completed</div>
                        <div class="fs-4 fw-bold text-secondary">{{ $statusCounts[3] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            {{-- FILTER BAR --}}
            <div class="card card-dark p-3 mb-3">
                <form class="row g-2 align-items-end" method="GET" action="{{ route('admin.projects.index') }}">
                    <div class="col-md-4">
                        <label class="form-label small mb-1">Search</label>
                        <input type="text" name="q" class="form-control" value="{{ $search ?? '' }}" placeholder="Title, description, or location">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All</option>
                            <option value="1" {{ ($statusFilter ?? '') == 1 ? 'selected' : '' }}>Active</option>
                            <option value="2" {{ ($statusFilter ?? '') == 2 ? 'selected' : '' }}>Pending</option>
                            <option value="3" {{ ($statusFilter ?? '') == 3 ? 'selected' : '' }}>Completed</option>
                            <option value="4" {{ ($statusFilter ?? '') == 4 ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Category</label>
                        <select name="category" class="form-select">
                            <option value="">All</option>
                            @php
                                $allCategories = collect();
                                foreach($categories as $parent){
                                    $allCategories->push(['id'=>$parent->id,'label'=>$parent->name]);
                                    foreach($parent->children as $child){
                                        $allCategories->push(['id'=>$child->id,'label'=>$parent->name.' > '. $child->name]);
                                    }
                                }
                            @endphp
                            @foreach($allCategories as $cat)
                                <option value="{{ $cat['id'] }}" {{ ($categoryId ?? '') == $cat['id'] ? 'selected' : '' }}>
                                    {{ $cat['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Sort</label>
                        <select name="sort" class="form-select">
                            <option value="newest" {{ ($sort ?? '')==='newest' ? 'selected' : '' }}>Newest</option>
                            <option value="oldest" {{ ($sort ?? '')==='oldest' ? 'selected' : '' }}>Oldest</option>
                            <option value="status" {{ ($sort ?? '')==='status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>
                    <div class="col-md-12 d-flex gap-2">
                        <button class="btn btn-gold">Apply</button>
                        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-dark">Reset</a>
                    </div>
                </form>
            </div>

            <div class="bg-light p-3 rounded">

                @php
                    $activeParent = $categories->first()->id ?? null;
                    foreach ($categories as $parent) {
                        if ($parent->children->contains(fn($c) => (string)$c->id === (string)($categoryId ?? ''))) {
                            $activeParent = $parent->id;
                            break;
                        }
                    }
                @endphp

                <div class="akg-newcard mb-4 p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="text-gold fw-bold mb-0">Project Categories</h5>
                        <div class="d-flex gap-2">
                            <button class="btn btn-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addParentModal">Parent Category</button>
                            <button class="btn btn-outline-gold btn-sm" data-bs-toggle="modal" data-bs-target="#addChildModal">Child Category</button>
                        </div>
                    </div>

                    @if($categories->count())

                    {{-- Parents nav --}}
                    <div class="parent-nav-box">
                        <ul class="nav nav-pills justify-content-start flex-wrap gap-2 mb-0" id="parentNav">
                            @foreach($categories as $parent)
                                <li class="nav-item">
                                    <div class="nav-link category-pill {{ $activeParent === $parent->id ? 'active' : '' }} d-flex align-items-center gap-2 text-dark" data-parent="{{ $parent->id }}">
                                        <a href="#" class="text-decoration-none text-dark fw-semibold" onclick="event.preventDefault(); switchParent({{ $parent->id }});">{{ $parent->name_localized }}</a>
                                        <div class="pill-actions d-flex gap-1">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-gold dropdown-toggle py-0 px-2" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editParentModal{{ $parent->id }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.projects.categories.destroy', $parent->id) }}" method="POST" onsubmit="return confirm('Delete this category?')" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="dropdown-item text-danger" type="submit">
                                                                <i class="bi bi-trash me-2"></i>Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Children nav --}}
                    @foreach($categories as $parent)
                        @php
                            $childTotal = $parent->children->sum('projects_count') + ($parent->projects_count ?? 0);
                        @endphp
                        <div class="child-bar {{ $activeParent === $parent->id ? '' : 'd-none' }}" id="children-{{ $parent->id }}">
                            <ul class="nav nav-pills justify-content-start flex-wrap gap-2">
                                <li class="nav-item">
                                    <div class="nav-link category-pill d-flex align-items-center gap-2 {{ $categoryId ? '' : 'active' }} text-dark">
                                        <a href="{{ route('admin.projects.index') }}" class="text-decoration-none text-dark fw-semibold">All</a>
                                        <span class="count-badge">{{ $childTotal }}</span>
                                    </div>
                                </li>
                                @foreach($parent->children as $child)
                                    <li class="nav-item">
                                        <div class="nav-link category-pill d-flex align-items-center gap-2 {{ (string)($categoryId ?? '') === (string)$child->id ? 'active' : '' }} text-dark">
                                            <a href="{{ route('admin.projects.index', ['category' => $child->id]) }}" class="text-decoration-none text-dark fw-semibold">
                                                {{ $child->name_localized }}
                                            </a>
                                            <span class="count-badge">{{ $child->projects_count ?? 0 }}</span>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-gold dropdown-toggle py-0 px-2" type="button" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editChildModal{{ $child->id }}">
                                                            <i class="bi bi-pencil me-2"></i>Edit
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.projects.categories.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Delete this category?')" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="dropdown-item text-danger" type="submit">
                                                                <i class="bi bi-trash me-2"></i>Delete
                                                            </button>
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

                {{-- Modals add/edit categories --}}
                <div class="modal fade" id="addParentModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content card-dark">
                            <form action="{{ route('admin.projects.categories.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Parent Category</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
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
                                    <div class="mb-3">
                                        <label class="form-label">Slug (optional)</label>
                                        <input type="text" name="slug" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Order</label>
                                        <input type="number" name="order" class="form-control" min="0">
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

                <div class="modal fade" id="addChildModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content card-dark">
                            <form action="{{ route('admin.projects.categories.store') }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Add Child Category</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Parent</label>
                                        <select name="parent_id" class="form-select" required>
                                            @foreach($categories as $parent)
                                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
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
                                    <div class="mb-3">
                                        <label class="form-label">Slug (optional)</label>
                                        <input type="text" name="slug" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Order</label>
                                        <input type="number" name="order" class="form-control" min="0">
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

                @foreach($categories as $parent)
                    <div class="modal fade" id="editParentModal{{ $parent->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content card-dark">
                                <form action="{{ route('admin.projects.categories.update', $parent->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Parent</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Name (Português)</label>
                                            <input type="text" name="name" class="form-control" value="{{ $parent->name }}" required>
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
                                        <div class="mb-3">
                                            <label class="form-label">Slug</label>
                                            <input type="text" name="slug" class="form-control" value="{{ $parent->slug }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Order</label>
                                            <input type="number" name="order" class="form-control" min="0" value="{{ $parent->order }}">
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

                @foreach($categories as $parent)
                    @foreach($parent->children as $child)
                        <div class="modal fade" id="editChildModal{{ $child->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content card-dark">
                                    <form action="{{ route('admin.projects.categories.update', $child->id) }}" method="POST">
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
                                                    @foreach($categories as $p)
                                                        <option value="{{ $p->id }}" {{ $child->parent_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
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
                                            <div class="mb-3">
                                                <label class="form-label">Slug</label>
                                                <input type="text" name="slug" class="form-control" value="{{ $child->slug }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Order</label>
                                                <input type="number" name="order" class="form-control" min="0" value="{{ $child->order }}">
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
                {{-- END CATEGORIES --}}

                {{-- SUCCESS MESSAGE --}}
                @if (session('success'))
                    <div class="alert alert-success fw-semibold">{{ session('success') }}</div>
                @endif


                {{-- ===================== PROJECTS TABLE ===================== --}}
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Main Image</th>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($projects as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                {{-- MAIN IMAGE --}}
                                <td>
                                    @php
                                        $image = $item->main_image;
                                        $src = asset('assets/img/default.jpg');
                                        if ($image) {
                                        if (str_contains($image, 'storage/public/assets/')) {
                                            $image = str_replace('storage/public/', '', $image);
                                        }
                                            if (str_starts_with($image, 'public/') || str_starts_with($image, 'assets/')) {
                                                $src = asset($image);
                                            } else {
                                                $src = asset('storage/' . $image);
                                            }
                                        }
                                    @endphp

                                    <img src="{{ $src }}" class="rounded shadow-sm"
                                         style="width:80px; height:60px; object-fit:cover;">
                                </td>


                                {{-- TITLE --}}
                                <td class="fw-semibold">
                                    {{ $item->title }}
                                    @if($item->location)
                                        <div class="text-muted small">{{ $item->location }}</div>
                                    @endif
                                </td>

                                {{-- STATUS --}}
                                <td>
                                    @php
                                        $statusText = match ($item->status) {
                                            1 => 'Active',
                                            2 => 'Pending',
                                            3 => 'Completed',
                                            4 => 'Archived',
                                            default => 'N/A',
                                        };

                                        $statusColor = match ($item->status) {
                                            1 => 'success',
                                            2 => 'warning',
                                            3 => 'secondary',
                                            4 => 'dark',
                                            default => 'dark',
                                        };
                                    @endphp

                                    <span class="badge bg-{{ $statusColor }}">
                                        {{ $statusText }}
                                    </span>
                                </td>

                                {{-- DATE --}}
                                <td>{{ $item->date ?? $item->created_at?->format('Y-m-d') ?? '—' }}</td>

                                {{-- ACTIONS --}}
                                <td>
                                    <div class="d-flex gap-2">

                                        {{-- EDIT --}}
                                        <a href="{{ route('admin.projects.edit', $item->id) }}"
                                            class="btn btn-sm btn-outline-gold fw-semibold px-3">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>

                                        {{-- DELETE --}}
                                        <form action="{{ route('admin.projects.destroy', $item->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this project?')">
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
                                    No projects found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                {{-- PAGINATION --}}
                <div class="mt-3">
                    {{ $projects->links() }}
                </div>

            </div>
        </div>

    </div>

    @push('scripts')
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
    @endpush
@endsection
