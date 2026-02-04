@extends('admin.layout-new')

@section('title', 'Account Details')
@section('page-title', 'WhatsApp Account Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Account Information</h5>
                    <div>
                        @if($account->status == 'connected')
                        <span class="badge bg-success">Connected</span>
                        @elseif($account->status == 'connecting')
                        <span class="badge bg-warning">Connecting</span>
                        @elseif($account->status == 'pending')
                        <span class="badge bg-info">Pending</span>
                        @else
                        <span class="badge bg-danger">{{ ucfirst($account->status) }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <th width="200">Phone Number:</th>
                                <td>{{ $account->phone_number }}</td>
                            </tr>
                            <tr>
                                <th>Account Name:</th>
                                <td>{{ $account->name }}</td>
                            </tr>
                            <tr>
                                <th>Owner:</th>
                                <td>{{ $account->user->name }} ({{ $account->user->email }})</td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    @if($account->status == 'connected')
                                    <span class="badge bg-success">Connected</span>
                                    @elseif($account->status == 'connecting')
                                    <span class="badge bg-warning">Connecting</span>
                                    @elseif($account->status == 'pending')
                                    <span class="badge bg-info">Pending</span>
                                    @else
                                    <span class="badge bg-danger">{{ ucfirst($account->status) }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Last Connected:</th>
                                <td>{{ $account->last_connected_at ? $account->last_connected_at->format('d M Y, H:i') : 'Never' }}</td>
                            </tr>
                            <tr>
                                <th>Created:</th>
                                <td>{{ $account->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex gap-2 mt-4">
                        <a href="{{ route('admin.accounts.edit', $account) }}" class="btn btn-primary">
                            <i class="bi bi-pencil me-2"></i> Edit
                        </a>
                        
                        <a href="{{ route('admin.accounts.webhook-setup', $account) }}" class="btn btn-info">
                            <i class="bi bi-link me-2"></i> Webhook Setup
                        </a>
                        
                        @if(!$account->is_verified)
                        <a href="{{ route('admin.accounts.verify', $account) }}" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i> Verify Phone Number
                        </a>
                        @else
                        <form action="{{ route('admin.accounts.disconnect', $account) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to disconnect this account?')">
                                <i class="bi bi-x-circle me-2"></i> Disconnect
                            </button>
                        </form>
                        @endif
                        
                        <form action="{{ route('admin.accounts.destroy', $account) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this account?')">
                                <i class="bi bi-trash me-2"></i> Delete
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary ms-auto">
                            <i class="bi bi-arrow-left me-2"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-key me-2"></i> API Credentials</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Sender Key</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $account->sender_key }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $account->sender_key }}')">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Sender Secret</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="senderSecret" value="{{ $account->sender_secret }}" readonly>
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleSecret()">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $account->sender_secret }}')">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>

                    <form action="{{ route('admin.accounts.regenerate', $account) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('This will invalidate the current keys. Continue?')">
                            <i class="bi bi-arrow-clockwise me-2"></i> Regenerate Keys
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-code-square me-2"></i> API Usage</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Endpoint:</strong></p>
                    <code class="d-block bg-light p-2 rounded mb-3">POST {{ url('/api/send') }}</code>
                    
                    <p class="mb-2"><strong>Example:</strong></p>
                    <pre class="bg-light p-2 rounded" style="font-size: 11px;">curl -X POST {{ url('/api/send') }} \
  -H "Content-Type: application/json" \
  -d '{
    "sender_key": "{{ $account->sender_key }}",
    "sender_secret": "{{ $account->sender_secret }}",
    "to": "628123456789",
    "message": "Hello!"
  }'</pre>
                </div>
            </div>
        </div>
    </div>

    @if($account->messages->count() > 0)
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recent Messages</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Recipient</th>
                                    <th>Message</th>
                                    <th>Status</th>
                                    <th>Sent At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($account->messages->take(10) as $message)
                                <tr>
                                    <td>{{ $message->recipient_number }}</td>
                                    <td>{{ \Str::limit($message->message, 50) }}</td>
                                    <td>
                                        @if($message->status == 'sent')
                                        <span class="badge bg-success">Sent</span>
                                        @elseif($message->status == 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                        @else
                                        <span class="badge bg-warning">{{ ucfirst($message->status) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $message->sent_at ? $message->sent_at->diffForHumans() : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Copied to clipboard!');
    });
}

function toggleSecret() {
    const input = document.getElementById('senderSecret');
    const icon = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye');
        icon.classList.add('bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash');
        icon.classList.add('bi-eye');
    }
}
</script>
@endsection
