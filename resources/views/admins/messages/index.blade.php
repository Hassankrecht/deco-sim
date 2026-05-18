@extends('layouts.admin')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-1 text-gold"><i class="bi bi-envelope me-2"></i>Contact Messages</h5>
                <p class="text-muted small mb-0">View and manage contact form submissions.</p>
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
                            <th style="width: 200px;">Sender</th>
                            <th style="width: 200px;">Email</th>
                            <th>Subject</th>
                            <th style="width: 150px;">Date</th>
                            <th style="width: 100px;">Status</th>
                            <th style="width: 150px;" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                            <tr class="{{ !$message->is_read ? 'table-warning' : '' }}">
                                <td>
                                    <div class="fw-semibold">{{ $message->name }}</div>
                                    @if($message->phone)
                                        <div class="text-muted small">{{ $message->phone }}</div>
                                    @endif
                                </td>
                                <td>
                                    <a href="mailto:{{ $message->email }}" class="text-light small">{{ $message->email }}</a>
                                </td>
                                <td class="small">
                                    <span title="{{ $message->subject }}">{{ \Illuminate\Support\Str::limit($message->subject, 50) }}</span>
                                </td>
                                <td class="text-muted small">
                                    {{ optional($message->created_at)->format('Y-m-d H:i') }}
                                </td>
                                <td>
                                    @if($message->is_read)
                                        <span class="badge bg-secondary">Read</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Unread</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-gold dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Actions
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a href="{{ route('admin.messages.show', $message) }}" class="dropdown-item">
                                                    <i class="bi bi-eye me-2"></i>View
                                                </a>
                                            </li>
                                            @if($message->is_read)
                                                <li>
                                                    <form action="{{ route('admin.messages.mark-unread', $message) }}" method="POST">
                                                        @csrf
                                                        <button class="dropdown-item" type="submit">
                                                            <i class="bi bi-envelope me-2"></i>Mark Unread
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{ route('admin.messages.mark-read', $message) }}" method="POST">
                                                        @csrf
                                                        <button class="dropdown-item" type="submit">
                                                            <i class="bi bi-check2 me-2"></i>Mark Read
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form action="{{ route('admin.messages.destroy', $message) }}" method="POST" onsubmit="return confirm('Delete this message?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="dropdown-item text-danger" type="submit">
                                                        <i class="bi bi-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No messages found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
