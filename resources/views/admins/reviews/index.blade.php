@extends('layouts.admin')

@section('content')
    @push('head')
        <style>
            .status-pill {
                padding: 6px 12px;
                border-radius: 999px;
                border: 1px solid #1f2937;
                background: #f8f9fb;
                color: #1f2937;
                font-weight: 600;
                font-size: 0.9rem;
            }
            .status-pill.active {
                background: linear-gradient(90deg, #c7954b, #d8aa65);
                color: #0f172a;
                border-color: #1f2937;
            }
        </style>
    @endpush

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
            <div>
                <h5 class="mb-1 text-gold"><i class="bi bi-chat-heart me-2"></i>{{ __('admin.testimonials') }}</h5>
                <p class="text-muted small mb-0">{{ __('admin.testimonials_subtitle') }}</p>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <a href="{{ route('admin.reviews.index', ['status' => 'pending', 'q' => $q]) }}"
                   class="status-pill {{ $status === 'pending' ? 'active' : '' }}">{{ __('admin.pending') }} ({{ $counts['pending'] ?? 0 }})</a>
                <a href="{{ route('admin.reviews.index', ['status' => 'approved', 'q' => $q]) }}"
                   class="status-pill {{ $status === 'approved' ? 'active' : '' }}">{{ __('admin.approved') }} ({{ $counts['approved'] ?? 0 }})</a>
                <a href="{{ route('admin.reviews.index', ['status' => 'all', 'q' => $q]) }}"
                   class="status-pill {{ $status === 'all' ? 'active' : '' }}">{{ __('admin.all') }} ({{ $counts['total'] ?? 0 }})</a>
                <form method="GET" action="{{ route('admin.reviews.index') }}" class="d-flex align-items-end gap-2">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div>
                        <label class="form-label small mb-1 text-muted">{{ __('admin.search') }}</label>
                        <input type="text" name="q" class="form-control form-control-sm" value="{{ $q ?? '' }}" placeholder="{{ __('admin.search_name_profession_review') }}">
                    </div>
                    <div class="d-flex gap-1 mb-1">
                        <button class="btn btn-sm btn-gold">{{ __('admin.apply') }}</button>
                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-sm btn-outline-dark">{{ __('admin.reset') }}</a>
                    </div>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success small">{{ session('success') }}</div>
        @endif

        <div class="card card-dark p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                <tr>
                    <th style="width: 170px;">{{ __('admin.client') }}</th>
                    <th style="width: 90px;">{{ __('admin.rating') }}</th>
                    <th>{{ __('admin.review') }}</th>
                    <th style="width: 140px;">{{ __('admin.submitted') }}</th>
                    <th style="width: 110px;">{{ __('admin.status') }}</th>
                    <th style="width: 220px;" class="text-end">{{ __('admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($reviews as $review)
                    <tr class="{{ $review->is_approved ? '' : 'table-warning' }}">
                        <td>
                            <div class="fw-semibold">{{ $review->name }}</div>
                            <div class="text-muted small">{{ $review->profession ?: '—' }}</div>
                        </td>
                        <td>
                            @if($review->rating)
                                <div class="text-warning d-flex align-items-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi {{ $i <= $review->rating ? 'bi-star-fill' : 'bi-star' }}"></i>
                                    @endfor
                                    <span class="text-muted small">({{ $review->rating }}/5)</span>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="small">
                            <span title="{{ $review->review }}">{{ \Illuminate\Support\Str::limit($review->review, 130) }}</span>
                        </td>
                        <td class="text-muted small">
                            {{ optional($review->created_at)->format('Y-m-d H:i') }}
                        </td>
                        <td>
                            @if($review->is_approved)
                                <span class="badge bg-success">{{ __('admin.approved') }}</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ __('admin.pending') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-gold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    {{ __('admin.actions') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if(!$review->is_approved)
                                        <li>
                                            <form action="{{ route('admin.reviews.approve', $review) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="dropdown-item" type="submit"><i class="bi bi-check2 me-2"></i>{{ __('admin.approve') }}</button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <form action="{{ route('admin.reviews.reject', $review) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="dropdown-item" type="submit"><i class="bi bi-eye-slash me-2"></i>{{ __('admin.hide') }}</button>
                                            </form>
                                        </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" onsubmit="return confirm('{{ __('admin.delete_review_confirm') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="dropdown-item text-danger" type="submit"><i class="bi bi-trash me-2"></i>{{ __('admin.delete') }}</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">{{ __('admin.no_reviews_found_filter') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $reviews->links() }}
        </div>
        </div>
    </div>
@endsection
