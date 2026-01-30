@extends('admin.layout-new')

@section('title', 'Verify WhatsApp')
@section('page-title', 'Verify WhatsApp Number')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <h5 class="mb-0"><i class="bi bi-phone me-2"></i> Verify WhatsApp Number</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4 text-center">
                        <h6 class="text-muted">Account: {{ $account->name }}</h6>
                        <p class="text-muted mb-0">{{ $account->phone_number }}</p>
                    </div>

                    @if(!$account->is_verified)
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i> Verification Steps:</h6>
                        <ol class="text-start mb-0">
                            <li>Click "Request Verification Code" below</li>
                            <li>You'll receive SMS or call to {{ $account->phone_number }}</li>
                            <li>Enter the 6-digit code you received</li>
                            <li>Your account will be verified and ready to use</li>
                        </ol>
                    </div>

                    <form action="{{ route('admin.accounts.request-code', $account) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-send me-2"></i> Request Verification Code
                        </button>
                    </form>

                    <form action="{{ route('admin.accounts.verify-code', $account) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">Verification Code</label>
                            <input type="text" class="form-control form-control-lg text-center" 
                                   id="code" name="code" maxlength="6" 
                                   placeholder="000000" required>
                            <div class="form-text">Enter the 6-digit code from SMS/Call</div>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-check-circle me-2"></i> Verify Account
                        </button>
                    </form>
                    @else
                    <div class="alert alert-success text-center">
                        <i class="bi bi-check-circle fs-1 d-block mb-3"></i>
                        <h5>Account Verified!</h5>
                        <p class="mb-0">Your WhatsApp account is ready to send messages.</p>
                    </div>
                    @endif

                    <div class="text-center mt-3">
                        <a href="{{ route('admin.accounts.show', $account) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i> Back to Account
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
