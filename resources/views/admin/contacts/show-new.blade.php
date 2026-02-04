@extends('admin.layout-new')

@section('title', 'Contact Details')
@section('page-title', 'Contact Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Contact Details</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.conversations.index', ['contact' => $contact->contact_number]) }}" class="btn btn-outline-primary">
                <i class="bi bi-chat-dots me-1"></i> View Conversation
            </a>
            <a href="{{ route('admin.contacts.edit', $contact) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Contact Info</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>Name:</strong> {{ $contact->name ?? '-' }}</div>
                    <div class="mb-2"><strong>Phone:</strong> {{ $contact->contact_number }}</div>
                    <div class="mb-2"><strong>Email:</strong> {{ $contact->email ?? '-' }}</div>
                    <div class="mb-2"><strong>Account:</strong> {{ $contact->whatsappAccount->name ?? '-' }}</div>
                    <div class="mb-2"><strong>Address:</strong><br>{{ $contact->address ?? '-' }}</div>
                    <div class="mb-2"><strong>Tags:</strong>
                        @if(!empty($contact->tags))
                            <div class="mt-1">
                                @foreach($contact->tags as $tag)
                                    <span class="badge bg-info text-dark me-1">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Conversation History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Direction</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($messages as $message)
                                <tr>
                                    <td class="align-middle">
                                        <span class="badge {{ $message->direction === 'incoming' ? 'bg-success' : 'bg-primary' }}">
                                            {{ ucfirst($message->direction) }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        {{ \Str::limit($message->message, 80) }}
                                        @if($message->media_url)
                                            <br><small class="text-muted"><i class="bi bi-paperclip me-1"></i> Has media</small>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($message->status === 'sent')
                                            <span class="badge bg-success">Sent</span>
                                        @elseif($message->status === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @elseif($message->status === 'delivered')
                                            <span class="badge bg-primary">Delivered</span>
                                        @elseif($message->status === 'read')
                                            <span class="badge bg-success">Read</span>
                                        @else
                                            <span class="badge bg-warning">{{ ucfirst($message->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">{{ $message->created_at?->format('d M Y, H:i') ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No messages found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($messages->hasPages())
                <div class="card-footer bg-white">
                    {{ $messages->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
