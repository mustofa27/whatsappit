@extends('admin.layout-new')

@section('title', 'Conversations')
@section('page-title', 'Conversations')

@section('content')
<style>
    .chat-container {
        display: flex;
        gap: 0;
        height: calc(100vh - 200px);
    }

    .conversations-list {
        flex: 0 0 350px;
        border-right: 1px solid #dee2e6;
        overflow-y: auto;
    }

    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background-color: #f8f9fa;
    }

    .message-bubble {
        display: flex;
        margin-bottom: 12px;
        animation: slideIn 0.3s ease-in-out;
    }

    .message-bubble.outgoing {
        justify-content: flex-end;
    }

    .message-bubble.incoming {
        justify-content: flex-start;
    }

    .bubble-content {
        max-width: 70%;
        padding: 12px 16px;
        border-radius: 12px;
        word-wrap: break-word;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .message-bubble.outgoing .bubble-content {
        background-color: #007bff;
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message-bubble.incoming .bubble-content {
        background-color: white;
        color: #333;
        border-bottom-left-radius: 4px;
    }

    .message-time {
        font-size: 12px;
        color: #999;
        margin-top: 4px;
        text-align: center;
    }

    .message-bubble.outgoing .message-time {
        text-align: right;
    }

    .message-bubble.incoming .message-time {
        text-align: left;
    }

    .conversation-item {
        padding: 12px 16px;
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        transition: background-color 0.2s;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .conversation-item:hover {
        background-color: #f8f9fa;
    }

    .conversation-item.active {
        background-color: #e7f3ff;
        border-left: 4px solid #007bff;
        padding-left: 12px;
    }

    .conversation-item.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background-color: #007bff;
    }

    .conversation-item-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        margin-bottom: 4px;
    }

    .conversation-item-name {
        font-weight: 500;
        color: #333;
        flex: 1;
    }

    .conversation-item-time {
        font-size: 12px;
        color: #999;
    }

    .conversation-item-preview {
        font-size: 13px;
        color: #666;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .unread-badge {
        margin-top: 4px;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Conversations</h2>
    </div>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ ($active_tab ?? 'conversations') === 'conversations' ? 'active' : '' }}"
               href="{{ route('admin.conversations.index') }}">
                <i class="bi bi-chat-dots me-1"></i> Conversations
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ ($active_tab ?? 'conversations') === 'log' ? 'active' : '' }}"
               href="{{ route('admin.conversations.index', ['tab' => 'log']) }}">
                <i class="bi bi-list-check me-1"></i> Message Log
            </a>
        </li>
    </ul>

    @if(($active_tab ?? 'conversations') === 'log')
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Message Log</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.conversations.index') }}" class="row g-2 align-items-end">
                    <input type="hidden" name="tab" value="log">
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="">All</option>
                            <option value="pending" {{ ($filters['status'] ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sent" {{ ($filters['status'] ?? '') === 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="delivered" {{ ($filters['status'] ?? '') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="read" {{ ($filters['status'] ?? '') === 'read' ? 'selected' : '' }}>Read</option>
                            <option value="failed" {{ ($filters['status'] ?? '') === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Account</label>
                        <select class="form-select" name="account_id">
                            <option value="">All</option>
                            @foreach(($accounts ?? []) as $acc)
                                <option value="{{ $acc->id }}" {{ (string)($filters['account_id'] ?? '') === (string)$acc->id ? 'selected' : '' }}>
                                    {{ $acc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Contact number or message...">
                    </div>
                    <div class="col-md-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.conversations.index', ['tab' => 'log']) }}" class="btn btn-outline-secondary w-100">Reset</a>
                    </div>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Account</th>
                                <th>Direction</th>
                                <th>Contact</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($message_log ?? [] as $message)
                            <tr>
                                <td class="align-middle">
                                    <small class="text-muted">{{ $message->whatsappAccount->name ?? '-' }}</small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge {{ $message->direction === 'incoming' ? 'bg-success' : 'bg-primary' }}">
                                        {{ ucfirst($message->direction) }}
                                    </span>
                                </td>
                                <td class="align-middle">{{ $message->contact_number ?? $message->receiver_number ?? '-' }}</td>
                                <td class="align-middle">
                                    {{ \Str::limit($message->message, 50) }}
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
                                <td colspan="6" class="text-center py-4 text-muted">No messages found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if(($message_log ?? null) && $message_log->hasPages())
            <div class="card-footer bg-white">
                {{ $message_log->links() }}
            </div>
            @endif
        </div>
    @else
    <div class="row">
        <!-- Conversations List -->
        <div class="col-md-4 d-flex flex-column" style="max-height: calc(100vh - 150px);">
            <div class="mb-3">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="searchInput" placeholder="Search conversations...">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3 d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary filter-btn active" data-filter="all">All</button>
                <button class="btn btn-sm btn-outline-warning filter-btn" data-filter="unread">Unread</button>
                <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="archived">Archived</button>
            </div>

            <div class="conversations-list flex-grow-1 border rounded">
                @forelse($conversations as $conv)
                <a href="{{ route('admin.conversations.index', ['contact' => $conv['contact_number']]) }}" 
                   class="text-decoration-none text-dark conversation-item {{ $selected_contact === $conv['contact_number'] ? 'active' : '' }}"
                   data-unread="{{ $conv['unread_count'] }}"
                   data-archived="{{ $conv['is_archived'] ? '1' : '0' }}">
                    <div class="conversation-item-header">
                        <span class="conversation-item-name">
                            {{ $conv['contact_name'] ?? 'Unknown' }}
                            @if($conv['is_archived'])
                            <span class="badge bg-secondary ms-2" style="font-size: 10px;">Archived</span>
                            @endif
                        </span>
                        <span class="conversation-item-time">{{ $conv['last_message_at']?->format('H:i') ?? '-' }}</span>
                    </div>
                    <div class="conversation-item-preview">{{ $conv['contact_number'] }}</div>
                    @if($conv['unread_count'] > 0)
                    <div class="unread-badge">
                        <span class="badge bg-danger">{{ $conv['unread_count'] }} new</span>
                    </div>
                    @endif
                </a>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="bi bi-chat-left-text" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-2">No conversations yet</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8 d-flex flex-column" style="max-height: calc(100vh - 150px);">
            @if($selected_contact && $selected_conversation)
            <div class="card shadow-sm h-100 d-flex flex-column">
                <!-- Chat Header -->
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $selected_conversation['contact_name'] ?? $selected_conversation['contact_number'] }}</h5>
                        <small class="text-muted">{{ $selected_conversation['contact_number'] }}</small>
                    </div>
                    <div class="d-flex gap-2">
                        @if($selected_conversation['unread_count'] > 0)
                            <form method="POST" action="{{ route('admin.conversations.mark-as-read', ['contact_number' => $selected_contact]) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-check-lg me-1"></i> Mark as Read
                                </button>
                            </form>
                        @endif

                        @if(!$selected_conversation['is_archived'])
                            <form method="POST" action="{{ route('admin.conversations.archive', ['contact_number' => $selected_contact]) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm text-white">
                                    <i class="bi bi-archive me-1"></i> Archive
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.conversations.unarchive', ['contact_number' => $selected_contact]) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i> Unarchive
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Messages -->
                <div class="messages-container" id="messagesContainer">
                    @forelse($messages as $message)
                    <div class="message-bubble {{ $message['direction'] }}">
                        <div>
                            <div class="bubble-content" style="max-width: none;">
                                @if($message['message'])
                                    {{ $message['message'] }}
                                @else
                                    <em>📎 Media attachment</em>
                                @endif
                            </div>
                            <div class="message-time">
                                {{ $message['created_at']->format('H:i') }}
                                @if($message['direction'] === 'outgoing')
                                    @if($message['status'] === 'delivered')
                                        <i class="bi bi-check2 ms-1" title="Delivered"></i>
                                    @elseif($message['status'] === 'read')
                                        <i class="bi bi-check2-all ms-1" title="Read"></i>
                                    @elseif($message['status'] === 'pending')
                                        <i class="bi bi-clock ms-1" title="Pending"></i>
                                    @elseif($message['status'] === 'failed')
                                        <i class="bi bi-exclamation-circle ms-1 text-danger" title="Failed"></i>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-chat-left-dots" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-2">No messages yet. Start the conversation!</p>
                    </div>
                    @endforelse
                </div>

                <!-- Message Input -->
                <div class="card-footer bg-white border-top">
                    <form action="{{ route('admin.conversations.send', ['contact_number' => $selected_contact]) }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="text" name="message" class="form-control" placeholder="Type a message..." required>
                        <button type="submit" class="btn btn-primary" title="Send message (Ctrl+Enter)">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="card shadow-sm h-100 d-flex align-items-center justify-content-center">
                <div class="text-center text-muted">
                    <i class="bi bi-chat-left-quote" style="font-size: 4rem; opacity: 0.3;"></i>
                    <p class="mt-3">Select a conversation to start chatting</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
    // Scroll active conversation into view
    function scrollActiveConversationIntoView() {
        const activeItem = document.querySelector('.conversation-item.active');
        const listContainer = document.querySelector('.conversations-list');
        if (activeItem && listContainer) {
            const itemTop = activeItem.offsetTop;
            const itemHeight = activeItem.offsetHeight;
            const containerHeight = listContainer.offsetHeight;
            const containerScrollTop = listContainer.scrollTop;
            
            // Scroll to center the active item
            if (itemTop < containerScrollTop) {
                listContainer.scrollTop = itemTop - 50;
            } else if (itemTop + itemHeight > containerScrollTop + containerHeight) {
                listContainer.scrollTop = itemTop + itemHeight - containerHeight + 50;
            }
        }
    }

    // Call on page load
    setTimeout(scrollActiveConversationIntoView, 100);

    // Add click handlers to all conversation items for active state
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            setTimeout(scrollActiveConversationIntoView, 50);
        });
    });

    // Auto-scroll to bottom of messages
    const messagesContainer = document.getElementById('messagesContainer');
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    // Search functionality (only if search input exists)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.conversation-item').forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    // Filter functionality
    const filterButtons = document.querySelectorAll('.filter-btn');
    if (filterButtons.length) {
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                filterButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                document.querySelectorAll('.conversation-item').forEach(item => {
                    const isArchived = item.dataset.archived === '1';
                    const unreadCount = parseInt(item.dataset.unread);
                    
                    if (filter === 'all') {
                        item.style.display = isArchived ? 'none' : 'block';
                    } else if (filter === 'unread') {
                        item.style.display = (unreadCount > 0 && !isArchived) ? 'block' : 'none';
                    } else if (filter === 'archived') {
                        item.style.display = isArchived ? 'block' : 'none';
                    }
                });
                
                setTimeout(scrollActiveConversationIntoView, 50);
            });
        });
    }

    // Send message with Ctrl+Enter
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && e.ctrlKey) {
                form.submit();
            }
        });
    });
</script>
@endsection
