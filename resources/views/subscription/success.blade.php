@extends('admin.layout-new')

@section('title', 'Payment Successful')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="mb-3">Payment Successful!</h2>
                    <p class="lead text-muted mb-4">
                        Thank you for subscribing. Your subscription is now active.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('subscription.show') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-box-seam me-2"></i> View My Subscription
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-house me-2"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
