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

                        <hr class="my-4">
                        <h6 class="mb-3">Meta WhatsApp API Credentials</h6>

                        <div class="mb-3">
                            <label for="phone_number_id" class="form-label">Phone Number ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone_number_id') is-invalid @enderror" 
                                   id="phone_number_id" name="phone_number_id" value="{{ old('phone_number_id', $account->phone_number_id) }}" 
                                   required placeholder="980422438489752">
                            <div class="form-text">Found in Meta WhatsApp API Setup page</div>
                            @error('phone_number_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="waba_id" class="form-label">WABA ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('waba_id') is-invalid @enderror" 
                                   id="waba_id" name="waba_id" value="{{ old('waba_id', $account->waba_id) }}" 
                                   required placeholder="114567812345678">
                            <div class="form-text">WhatsApp Business Account ID from Meta</div>
                            @error('waba_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="access_token" class="form-label">Access Token <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('access_token') is-invalid @enderror" 
                                       id="access_token" name="access_token" value="{{ old('access_token', $account->access_token) }}" 
                                       required>
                                <button class="btn btn-outline-secondary" type="button" id="toggleAccessToken">
                                    <i class="bi bi-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <div class="form-text">Meta API access token (from Getting Started page)</div>
                            @error('access_token')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <script>
                            document.getElementById('toggleAccessToken').addEventListener('click', function() {
                                const input = document.getElementById('access_token');
                                const icon = document.getElementById('toggleIcon');
                                
                                if (input.type === 'password') {
                                    input.type = 'text';
                                    icon.classList.remove('bi-eye');
                                    icon.classList.add('bi-eye-slash');
                                } else {
                                    input.type = 'password';
                                    icon.classList.remove('bi-eye-slash');
                                    icon.classList.add('bi-eye');
                                }
                            });
                        </script>

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
