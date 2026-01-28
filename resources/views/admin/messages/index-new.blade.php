@extends('admin.layout-new')

@section('title', 'Messages')
@section('page-title', 'Messages')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">All Messages</h2>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Messages ({{ $messages->total() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Account</th>
                            <th>Recipient</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Sent At</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                        <tr>
                            <td class="align-middle">
                                <small class="text-muted">{{ $message->whatsappAccount->name }}</small>
                            </td>
                            <td class="align-middle">{{ $message->recipient_number }}</td>
                            <td class="align-middle">
                                {{ \Str::limit($message->message, 50) }}
                                @if($message->media_url)
                                <br><small class="text-muted"><i class="bi bi-image me-1"></i> Has media</small>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($message->status == 'sent')
                                    <span class="badge bg-success">Sent</span>
                                @elseif($message->status == 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($message->status) }}</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{ $message->sent_at ? $message->sent_at->format('d M Y, H:i') : '-' }}
                            </td>
                            <td class="align-middle text-end">
                                <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No messages found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $messages->links() }}
    </div>
</div>
@endsection
