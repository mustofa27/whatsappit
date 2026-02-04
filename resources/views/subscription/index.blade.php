@extends('admin.layout-new')

@section('title', 'Choose Your Plan')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold">Choose Your Plan</h1>
        <p class="lead text-muted">Select the perfect plan for your business needs</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            {{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($currentSubscription)
        <div class="alert alert-success mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Active Subscription:</strong> {{ $currentSubscription->plan->name }}
                    - Expires on {{ $currentSubscription->expires_at->format('d M Y') }}
                    ({{ $currentSubscription->daysRemaining() }} days remaining)
                </div>
                <a href="{{ route('subscription.show') }}" class="btn btn-sm btn-outline-success">
                    Manage Subscription
                </a>
            </div>
        </div>
    @endif

    <div class="row g-4">
        @foreach($plans as $plan)
            <div class="col-md-4">
                <div class="card h-100 {{ $plan->is_popular ? 'border-primary shadow' : '' }}">
                    @if($plan->is_popular)
                        <div class="card-header bg-primary text-white text-center">
                            <i class="bi bi-star-fill me-1"></i> Most Popular
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title">{{ $plan->name }}</h3>
                        <div class="mb-3">
                            <h2 class="display-5 fw-bold">{{ $plan->formatted_price }}</h2>
                            <span class="text-muted">/month</span>
                        </div>
                        
                        @if($plan->description)
                            <p class="text-muted">{{ $plan->description }}</p>
                        @endif

                        <ul class="list-unstyled mb-4">
                            @foreach($plan->features as $feature)
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    {{ $feature }}
                                </li>
                            @endforeach
                            
                            @if($plan->max_accounts)
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Up to {{ $plan->max_accounts }} WhatsApp accounts
                                </li>
                            @endif
                            
                            @if($plan->max_users)
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Up to {{ $plan->max_users }} team members
                                </li>
                            @endif
                        </ul>

                        <div class="mt-auto">
                            @if($currentSubscription && $currentSubscription->subscription_plan_id === $plan->id)
                                <button class="btn btn-success w-100" disabled>
                                    <i class="bi bi-check-circle me-1"></i> Current Plan
                                </button>
                            @elseif($currentSubscription)
                                <button class="btn btn-outline-primary w-100" disabled>
                                    <i class="bi bi-lock me-1"></i> Already Subscribed
                                </button>
                            @else
                                <form action="{{ route('subscription.subscribe', $plan) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn {{ $plan->is_popular ? 'btn-primary' : 'btn-outline-primary' }} w-100">
                                        <i class="bi bi-credit-card me-1"></i> Subscribe Now
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if($plans->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h3 class="mt-3">No Plans Available</h3>
            <p class="text-muted">Please contact administrator for subscription options.</p>
        </div>
    @endif
</div>
@endsection
