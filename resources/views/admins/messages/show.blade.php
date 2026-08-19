@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 text-gold"><i class="bi bi-envelope me-2"></i>{{ __('admin.message_details') }}</h5>
                <p class="text-muted small mb-0">{{ __('admin.message_details_subtitle') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-outline-gold">{{ __('admin.back_to_messages') }}</a>
                @if($message->is_read)
                    <form action="{{ route('admin.messages.mark-unread', $message) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-dark">{{ __('admin.mark_unread') }}</button>
                    </form>
                @else
                    <form action="{{ route('admin.messages.mark-read', $message) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-gold">{{ __('admin.mark_read') }}</button>
                    </form>
                @endif
                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('{{ __('admin.delete_message_confirm') }}');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">{{ __('admin.delete') }}</button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success small">{{ session('success') }}</div>
        @endif

        <div class="card card-dark p-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="text-muted small">{{ __('admin.from') }}</label>
                        <div class="fw-semibold">{{ $message->name }}</div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">{{ __('admin.email') }}</label>
                        <div>
                            <a href="mailto:{{ $message->email }}" class="text-light">{{ $message->email }}</a>
                        </div>
                    </div>
                    @if($message->phone)
                        <div class="mb-2">
                            <label class="text-muted small">{{ __('admin.phone') }}</label>
                            <div>{{ $message->phone }}</div>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="text-muted small">{{ __('admin.subject') }}</label>
                        <div class="fw-semibold">{{ $message->subject }}</div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">{{ __('admin.status') }}</label>
                        <div>
                            @if($message->is_read)
                                <span class="badge bg-secondary">{{ __('admin.read') }}</span>
                            @else
                                <span class="badge bg-warning text-dark">{{ __('admin.unread') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">{{ __('admin.received') }}</label>
                        <div>{{ optional($message->created_at)->format('Y-m-d H:i:s') }}</div>
                    </div>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="mb-3">
                <label class="text-muted small">{{ __('admin.message') }}</label>
                <div class="p-3 bg-dark rounded mt-2" style="white-space: pre-wrap;">{{ $message->message }}</div>
            </div>
        </div>
    </div>
@endsection
