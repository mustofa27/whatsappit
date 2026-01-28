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
                    <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i> Information</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>What happens next?</strong></p>
                    <ol class="ps-3 mb-0">
                        <li class="mb-2">Your account will be created</li>
                        <li class="mb-2">You'll be redirected to scan a QR code</li>
                        <li class="mb-2">Scan with your WhatsApp mobile app</li>
                        <li>Account will be connected and ready to use!</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
