@extends('admin.layout-new')

@section('title', 'Payment Failed')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="mb-3">Payment Failed</h2>
                    <p class="lead text-muted mb-4">
                        Unfortunately, your payment could not be processed. Please try again.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('subscription.index') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-clockwise me-2"></i> Try Again
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
