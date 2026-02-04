@extends('admin.layout-new')

@section('title', 'Message Queue')
@section('page-title', 'Message Queue & Scheduling')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Message Queue & Scheduling</h2>
        <a href="{{ route('admin.scheduled-messages.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Schedule New Message
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.scheduled-messages.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Recipient or message" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Account</label>
                    <select name="account_id" class="form-select">
                        <option value="">All Accounts</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.scheduled-messages.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <form id="bulkActionForm" method="POST" action="{{ route('admin.scheduled-messages.bulk-action') }}">
        @csrf
        <input type="hidden" name="action" id="bulkAction">
        
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Scheduled Messages ({{ $scheduledMessages->total() }})</h5>
                <div>
                    <button type="button" class="btn btn-sm btn-warning" onclick="submitBulkAction('cancel')">
                        <i class="bi bi-x-circle me-1"></i> Cancel Selected
                    </button>
                    <button type="button" class="btn btn-sm btn-info" onclick="submitBulkAction('retry')">
                        <i class="bi bi-arrow-clockwise me-1"></i> Retry Selected
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="submitBulkAction('delete')">
                        <i class="bi bi-trash me-1"></i> Delete Selected
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Recipient</th>
                            <th>Message</th>
                            <th>Account</th>
                            <th>Scheduled At</th>
                            <th>Status</th>
                            <th>Retries</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($scheduledMessages as $message)
                            <tr>
                                <td>
                                    <input type="checkbox" name="message_ids[]" value="{{ $message->id }}" class="message-checkbox">
                                </td>
                                <td>
                                    <strong>{{ $message->recipient_number }}</strong>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 300px;" title="{{ $message->message_content }}">
                                        {{ $message->message_content }}
                                    </div>
                                    @if($message->template_name)
                                        <small class="text-muted">
                                            <i class="bi bi-file-text"></i> Template: {{ $message->template_name }}
                                        </small>
                                    @endif
                                </td>
                                <td>{{ $message->whatsappAccount->name }}</td>
                                <td>
                                    {{ $message->scheduled_at->format('Y-m-d H:i') }}
                                    <br>
                                    <small class="text-muted">{{ $message->scheduled_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($message->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @elseif($message->status === 'processing')
                                        <span class="badge bg-info">Processing</span>
                                    @elseif($message->status === 'sent')
                                        <span class="badge bg-success">Sent</span>
                                    @elseif($message->status === 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @elseif($message->status === 'cancelled')
                                        <span class="badge bg-secondary">Cancelled</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $message->retry_count }} / {{ $message->max_retries }}
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.scheduled-messages.show', $message) }}" class="btn btn-sm btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        @if(in_array($message->status, ['pending', 'failed']))
                                            <a href="{{ route('admin.scheduled-messages.edit', $message) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif
                                        @if($message->status === 'pending')
                                            <form action="{{ route('admin.scheduled-messages.cancel', $message) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-warning" title="Cancel" onclick="return confirm('Cancel this scheduled message?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($message->status === 'failed')
                                            <form action="{{ route('admin.scheduled-messages.retry', $message) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-info" title="Retry">
                                                    <i class="bi bi-arrow-clockwise"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($message->status !== 'processing')
                                            <form action="{{ route('admin.scheduled-messages.destroy', $message) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this scheduled message?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox display-1 text-muted"></i>
                                    <p class="text-muted mt-2">No scheduled messages found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($scheduledMessages->hasPages())
                <div class="card-footer">
                    {{ $scheduledMessages->links() }}
                </div>
            @endif
        </div>
    </form>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.message-checkbox').forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

function submitBulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.message-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Please select at least one message');
        return;
    }
    
    let confirmMsg = '';
    if (action === 'cancel') confirmMsg = 'Cancel selected messages?';
    else if (action === 'delete') confirmMsg = 'Delete selected messages?';
    else if (action === 'retry') confirmMsg = 'Retry selected failed messages?';
    
    if (confirm(confirmMsg)) {
        document.getElementById('bulkAction').value = action;
        document.getElementById('bulkActionForm').submit();
    }
}
</script>
@endsection
