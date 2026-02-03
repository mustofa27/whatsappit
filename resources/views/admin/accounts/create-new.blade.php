@extends('admin.layout-new')

@section('title', 'Create WhatsApp Account')
@section('page-title', 'Create WhatsApp Account')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">New WhatsApp Account</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.accounts.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" name="phone_number" value="{{ old('phone_number') }}" 
                                   required placeholder="628123456789">
                            <div class="form-text">Enter phone number with country code (e.g., 628123456789 for Indonesia)</div>
                            @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">Account Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   required placeholder="My WhatsApp Account">
                            <div class="form-text">A friendly name to identify this account</div>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">
                        <h6 class="mb-3">Meta WhatsApp API Credentials</h6>

                        <div class="mb-3">
                            <label for="phone_number_id" class="form-label">Phone Number ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('phone_number_id') is-invalid @enderror" 
                                   id="phone_number_id" name="phone_number_id" value="{{ old('phone_number_id') }}" 
                                   required placeholder="980422438489752">
                            <div class="form-text">Found in Meta WhatsApp API Setup page</div>
                            @error('phone_number_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="waba_id" class="form-label">WABA ID <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('waba_id') is-invalid @enderror" 
                                   id="waba_id" name="waba_id" value="{{ old('waba_id') }}" 
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
                                       id="access_token" name="access_token" value="{{ old('access_token') }}" 
                                       required placeholder="EAAxxxxxxxxx...">
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
                                <i class="bi bi-plus-circle me-2"></i> Create Account
                            </button>
                            <a href="{{ route('admin.accounts.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Where to Find Credentials</h6>
                </div>
                <div class="card-body small">
                    <p class="mb-2"><strong>Phone Number ID & WABA ID:</strong></p>
                    <p class="mb-3">Go to <a href="https://developers.facebook.com" target="_blank" class="text-decoration-none">developers.facebook.com</a> → WhatsApp App → API Setup</p>
                    
                    <p class="mb-2"><strong>Access Token:</strong></p>
                    <p class="mb-3">Click "Generate access token" in the Getting Started tab</p>
                    
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> Use a permanent System User token, not temporary tokens
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
