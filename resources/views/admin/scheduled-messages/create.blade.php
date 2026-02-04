@extends('admin.layout-new')

@section('title', 'Schedule Message')
@section('page-title', 'Schedule New Message')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Schedule New Message</h2>
        <a href="{{ route('admin.scheduled-messages.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.scheduled-messages.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="whatsapp_account_id" class="form-label">WhatsApp Account <span class="text-danger">*</span></label>
                            <select class="form-select @error('whatsapp_account_id') is-invalid @enderror" 
                                    id="whatsapp_account_id" name="whatsapp_account_id" required>
                                <option value="">Select Account</option>
                                @forelse($accounts as $account)
                                    <option value="{{ $account->id }}" {{ old('whatsapp_account_id') == $account->id ? 'selected' : '' }}>
                                        {{ $account->name }}
                                    </option>
                                @empty
                                    <option value="" disabled>No accounts available - Please create a WhatsApp account first</option>
                                @endforelse
                            </select>
                            @error('whatsapp_account_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if(isset($accounts) && $accounts->isEmpty())
                                <small class="text-warning d-block mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    No WhatsApp accounts found. <a href="{{ route('admin.accounts.create') }}" class="text-warning">Create one now</a>
                                </small>
                            @endif
                        </div>

                        <div class="mb-3">
                            <label for="recipient_number" class="form-label">Recipient Number <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('recipient_number') is-invalid @enderror" 
                                   id="recipient_number" 
                                   name="recipient_number" 
                                   value="{{ old('recipient_number') }}"
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
                                      required>{{ old('message_content') }}</textarea>
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
                                           value="{{ old('scheduled_at') }}"
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
                                           value="{{ old('max_retries', 3) }}"
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
                                <i class="bi bi-clock-history"></i> Schedule Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm bg-light">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-1"></i> Scheduling Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Messages must be scheduled at least 5 minutes in advance</li>
                        <li>Use country code format for recipient numbers (e.g., 628xxx)</li>
                        <li>Messages are processed every minute by the scheduler</li>
                        <li>Failed messages will retry automatically based on max retries setting</li>
                        <li>You can cancel pending messages before they are sent</li>
                        <li>Rate limiting: 1 message per second per account</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm bg-light mt-3">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0"><i class="bi bi-clock-history me-1"></i> Quick Schedule</h6>
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
