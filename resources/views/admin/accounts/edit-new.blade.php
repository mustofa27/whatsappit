@extends('admin.layout-new')

@section('title', 'Edit WhatsApp Account')
@section('page-title', 'Edit WhatsApp Account')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Edit Account Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.accounts.update', $account) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Owner</label>
                            <input type="text" class="form-control" value="{{ $account->user->name }} ({{ $account->user->email }})" disabled>
                        </div>

                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" name="phone_number" value="{{ old('phone_number', $account->phone_number) }}" 
                                   required placeholder="628123456789">
                            @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Account Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $account->name) }}" 
                                   required placeholder="My WhatsApp Account">
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="connected" {{ old('status', $account->status) == 'connected' ? 'selected' : '' }}>Connected</option>
                                <option value="connecting" {{ old('status', $account->status) == 'connecting' ? 'selected' : '' }}>Connecting</option>
                                <option value="disconnected" {{ old('status', $account->status) == 'disconnected' ? 'selected' : '' }}>Disconnected</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i> Update Account
                            </button>
                            <a href="{{ route('admin.accounts.show', $account) }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
