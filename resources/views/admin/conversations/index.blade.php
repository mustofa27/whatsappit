@extends('admin.layout-new')

@section('title', 'Conversations')
@section('page-title', 'Conversations')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Conversations</h2>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">All Conversations ({{ count($conversations) }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Contact</th>
                            <th>Phone</th>
                            <th>Unread</th>
                            <th>Last Message</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conversations as $conv)
                        <tr class="{{ $selected_contact === $conv['contact_number'] ? 'table-primary' : '' }}">
                            <td class="align-middle">
                                {{ $conv['contact_name'] ?? 'Unknown' }}
                            </td>
                            <td class="align-middle">{{ $conv['contact_number'] }}</td>
                            <td class="align-middle">
                                @if($conv['unread_count'] > 0)
                                    <span class="badge bg-warning">{{ $conv['unread_count'] }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{ $conv['last_message_at'] ? $conv['last_message_at']->format('d M Y, H:i') : 'No messages' }}
                            </td>
                            <td class="align-middle text-end">
                                <a href="{{ route('admin.conversations.index', ['contact' => $conv['contact_number']]) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No conversations found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($selected_contact && $selected_conversation)
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <div>
                <h5 class="mb-1">{{ $selected_conversation['contact_name'] ?? $selected_conversation['contact_number'] }}</h5>
                <small class="text-muted">{{ $selected_conversation['contact_number'] }}</small>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @if($selected_conversation['unread_count'] > 0)
                    <form method="POST" action="{{ route('admin.conversations.mark-as-read', ['contact_number' => $selected_contact]) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="bi bi-check-lg me-1"></i> Mark as Read
                        </button>
                    </form>
                @endif

                @if(!$selected_conversation['is_archived'])
                    <form method="POST" action="{{ route('admin.conversations.archive', ['contact_number' => $selected_contact]) }}">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm text-white">
                            <i class="bi bi-archive me-1"></i> Archive
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.conversations.unarchive', ['contact_number' => $selected_contact]) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Unarchive
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Direction</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                        <tr>
                            <td class="align-middle">
                                <span class="badge {{ $message['direction'] === 'incoming' ? 'bg-success' : 'bg-primary' }}">
                                    {{ ucfirst($message['direction']) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                {{ $message['message'] ? \Str::limit($message['message'], 60) : '[Media]' }}
                                @if($message['media_url'])
                                    <br><small class="text-muted"><i class="bi bi-paperclip me-1"></i> Has media</small>
                                @endif
                            </td>
                            <td class="align-middle">{{ ucfirst($message['message_type'] ?? 'text') }}</td>
                            <td class="align-middle">
                                @if($message['direction'] === 'incoming')
                                    <span class="badge bg-success">Received</span>
                                @else
                                    @if(in_array($message['status'], ['sent', 'delivered', 'read']))
                                        <span class="badge bg-success">{{ ucfirst($message['status']) }}</span>
                                    @elseif($message['status'] === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Failed</span>
                                    @endif
                                @endif
                            </td>
                            <td class="align-middle">{{ $message['created_at']->format('d M Y, H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No messages yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white">
            <form action="{{ route('admin.conversations.send', ['contact_number' => $selected_contact]) }}" method="POST" class="d-flex gap-2">
                @csrf
                <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-1"></i> Send
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="card shadow-sm">
        <div class="card-body text-center text-muted py-5">
            Select a conversation to view messages.
        </div>
    </div>
    @endif
</div>
@endsection
