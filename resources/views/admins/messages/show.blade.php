@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 text-gold"><i class="bi bi-envelope me-2"></i>Message Details</h5>
                <p class="text-muted small mb-0">View full contact message.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.messages.index') }}" class="btn btn-sm btn-outline-gold">Back to Messages</a>
                @if($message->is_read)
                    <form action="{{ route('admin.messages.mark-unread', $message) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-dark">Mark Unread</button>
                    </form>
                @else
                    <form action="{{ route('admin.messages.mark-read', $message) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-gold">Mark Read</button>
                    </form>
                @endif
                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Delete this message?');" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
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
                        <label class="text-muted small">From</label>
                        <div class="fw-semibold">{{ $message->name }}</div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Email</label>
                        <div>
                            <a href="mailto:{{ $message->email }}" class="text-light">{{ $message->email }}</a>
                        </div>
                    </div>
                    @if($message->phone)
                        <div class="mb-2">
                            <label class="text-muted small">Phone</label>
                            <div>{{ $message->phone }}</div>
                        </div>
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label class="text-muted small">Subject</label>
                        <div class="fw-semibold">{{ $message->subject }}</div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Status</label>
                        <div>
                            @if($message->is_read)
                                <span class="badge bg-secondary">Read</span>
                            @else
                                <span class="badge bg-warning text-dark">Unread</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted small">Received</label>
                        <div>{{ optional($message->created_at)->format('Y-m-d H:i:s') }}</div>
                    </div>
                </div>
            </div>
            <hr class="border-secondary">
            <div class="mb-3">
                <label class="text-muted small">Message</label>
                <div class="p-3 bg-dark rounded mt-2" style="white-space: pre-wrap;">{{ $message->message }}</div>
            </div>
        </div>
    </div>
@endsection
