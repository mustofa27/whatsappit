@extends('admin.layout-new')

@section('title', 'Message Details')
@section('page-title', 'Scheduled Message Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Scheduled Message Details</h2>
        <div>
            @if(in_array($scheduledMessage->status, ['pending', 'failed']))
                <a href="{{ route('admin.scheduled-messages.edit', $scheduledMessage) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i> Edit
                </a>
            @endif
            <a href="{{ route('admin.scheduled-messages.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Message Content</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="fw-bold">Message:</label>
                        <div class="border rounded p-3 bg-light">
                            {{ $scheduledMessage->message_content }}
                        </div>
                    </div>

                    @if($scheduledMessage->template_name)
                        <div class="mb-3">
                            <label class="fw-bold">Template:</label>
                            <p>{{ $scheduledMessage->template_name }}</p>
                            @if($scheduledMessage->template_params)
                                <label class="fw-bold">Template Parameters:</label>
                                <pre class="bg-light p-2 rounded">{{ json_encode($scheduledMessage->template_params, JSON_PRETTY_PRINT) }}</pre>
                            @endif
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label class="fw-bold">Recipient:</label>
                            <p>{{ $scheduledMessage->recipient_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="fw-bold">WhatsApp Account:</label>
                            <p>{{ $scheduledMessage->whatsappAccount->name }}</p>
                        </div>
                    </div>

                    @if($scheduledMessage->error_message)
                        <div class="alert alert-danger">
                            <strong><i class="bi bi-exclamation-triangle"></i> Error:</strong><br>
                            {{ $scheduledMessage->error_message }}
                        </div>
                    @endif
                </div>
            </div>

            @if($scheduledMessage->status === 'sent' && $scheduledMessage->meta_message_id)
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Delivery Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="fw-bold">Meta Message ID:</label>
                                <p class="font-monospace">{{ $scheduledMessage->meta_message_id }}</p>
                            </div>
                            <div class="col-md-6">
                                <label class="fw-bold">Sent At:</label>
                                <p>{{ $scheduledMessage->sent_at?->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0">Status & Schedule</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @if($scheduledMessage->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($scheduledMessage->status === 'processing')
                                <span class="badge bg-info">Processing</span>
                            @elseif($scheduledMessage->status === 'sent')
                                <span class="badge bg-success">Sent</span>
                            @elseif($scheduledMessage->status === 'failed')
                                <span class="badge bg-danger">Failed</span>
                            @elseif($scheduledMessage->status === 'cancelled')
                                <span class="badge bg-secondary">Cancelled</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Scheduled At:</dt>
                        <dd class="col-sm-7">
                            {{ $scheduledMessage->scheduled_at->format('Y-m-d H:i:s') }}
                            <br>
                            <small class="text-muted">{{ $scheduledMessage->scheduled_at->diffForHumans() }}</small>
                        </dd>

                        <dt class="col-sm-5">Retry Count:</dt>
                        <dd class="col-sm-7">{{ $scheduledMessage->retry_count }} / {{ $scheduledMessage->max_retries }}</dd>

                        <dt class="col-sm-5">Created At:</dt>
                        <dd class="col-sm-7">
                            <small>{{ $scheduledMessage->created_at->format('Y-m-d H:i:s') }}</small>
                        </dd>

                        <dt class="col-sm-5">Updated At:</dt>
                        <dd class="col-sm-7">
                            <small>{{ $scheduledMessage->updated_at->format('Y-m-d H:i:s') }}</small>
                        </dd>
                    </dl>

                    <hr>

                    <div class="d-grid gap-2">
                        @if($scheduledMessage->status === 'pending')
                            <form action="{{ route('admin.scheduled-messages.cancel', $scheduledMessage) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Cancel this scheduled message?')">
                                    <i class="bi bi-x-circle"></i> Cancel Message
                                </button>
                            </form>
                        @endif

                        @if($scheduledMessage->status === 'failed')
                            <form action="{{ route('admin.scheduled-messages.retry', $scheduledMessage) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="bi bi-arrow-clockwise"></i> Retry Now
                                </button>
                            </form>
                        @endif

                        @if($scheduledMessage->status !== 'processing')
                            <form action="{{ route('admin.scheduled-messages.destroy', $scheduledMessage) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Delete this scheduled message?')">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            @if($scheduledMessage->status === 'failed')
                <div class="card shadow-sm mt-3 border-danger">
                    <div class="card-header bg-danger text-white border-bottom">
                        <h6 class="mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Troubleshooting</h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>Check recipient number format</li>
                            <li>Verify WhatsApp account credentials</li>
                            <li>Ensure message content is valid</li>
                            <li>Check Meta API rate limits</li>
                            <li>Review error message above</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
