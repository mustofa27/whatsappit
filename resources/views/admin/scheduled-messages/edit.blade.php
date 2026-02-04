@extends('admin.layout-new')

@section('title', 'Edit Scheduled Message')
@section('page-title', 'Edit Scheduled Message')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Edit Scheduled Message</h2>
        <a href="{{ route('admin.scheduled-messages.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.scheduled-messages.update', $scheduledMessage) }}">
                        @csrf
                        @method('PUT')

                        @if($scheduledMessage->status === 'failed')
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                This message failed. Editing will reset its status to pending and retry count to 0.
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="whatsapp_account_id" class="form-label">WhatsApp Account <span class="text-danger">*</span></label>
                            <select class="form-select @error('whatsapp_account_id') is-invalid @enderror" 
                                    id="whatsapp_account_id" name="whatsapp_account_id" required>
                                <option value="">Select Account</option>
                                @forelse($accounts as $account)
                                    <option value="{{ $account->id }}" 
                                        {{ (old('whatsapp_account_id', $scheduledMessage->whatsapp_account_id) == $account->id) ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @empty
                                    <option value="" disabled>No accounts available</option>
                                @endforelse
                            </select>
                            @error('whatsapp_account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="recipient_number" class="form-label">Recipient Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('recipient_number') is-invalid @enderror" 
                                   id="recipient_number" 
                                   name="recipient_number" 
                                   value="{{ old('recipient_number', $scheduledMessage->recipient_number) }}"
                                   placeholder="628123456789"
                                   required>
                            <small class="text-muted">Format: Country code + number (e.g., 628123456789)</small>
                            @error('recipient_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message_content" class="form-label">Message Content <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message_content') is-invalid @enderror" 
                                      id="message_content" 
                                      name="message_content" 
                                      rows="5" 
                                      required>{{ old('message_content', $scheduledMessage->message_content) }}</textarea>
                            <small class="text-muted">Maximum 4096 characters</small>
                            @error('message_content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="scheduled_at" class="form-label">Schedule Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" 
                                           class="form-control @error('scheduled_at') is-invalid @enderror" 
                                           id="scheduled_at" 
                                           name="scheduled_at" 
                                           value="{{ old('scheduled_at', $scheduledMessage->scheduled_at->format('Y-m-d\TH:i')) }}"
                                           min="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}"
                                           required>
                                    <small class="text-muted">Must be at least 5 minutes in the future</small>
                                    @error('scheduled_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_retries" class="form-label">Max Retries</label>
                                    <input type="number" 
                                           class="form-control @error('max_retries') is-invalid @enderror" 
                                           id="max_retries" 
                                           name="max_retries" 
                                           value="{{ old('max_retries', $scheduledMessage->max_retries) }}"
                                           min="0"
                                           max="5">
                                    <small class="text-muted">Number of retry attempts if message fails (0-5)</small>
                                    @error('max_retries')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Scheduled Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-1"></i> Message Details</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Status:</dt>
                        <dd class="col-sm-7">
                            @if($scheduledMessage->status === 'pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($scheduledMessage->status === 'failed')
                                <span class="badge bg-danger">Failed</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Retry Count:</dt>
                        <dd class="col-sm-7">{{ $scheduledMessage->retry_count }} / {{ $scheduledMessage->max_retries }}</dd>

                        @if($scheduledMessage->error_message)
                            <dt class="col-sm-5">Last Error:</dt>
                            <dd class="col-sm-7">
                                <small class="text-danger">{{ $scheduledMessage->error_message }}</small>
                            </dd>
                        @endif

                        <dt class="col-sm-5">Created:</dt>
                        <dd class="col-sm-7">
                            <small>{{ $scheduledMessage->created_at->format('Y-m-d H:i') }}</small>
                        </dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm bg-light mt-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-1"></i> Quick Reschedule</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="setSchedule(5)">
                            +5 minutes
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="setSchedule(30)">
                            +30 minutes
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="setSchedule(60)">
                            +1 hour
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="setSchedule(1440)">
                            +1 day
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setSchedule(minutes) {
    const now = new Date();
    now.setMinutes(now.getMinutes() + minutes);
    const formatted = now.toISOString().slice(0, 16);
    document.getElementById('scheduled_at').value = formatted;
}
</script>
@endsection
