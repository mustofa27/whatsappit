@extends('admin.layout-new')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Subscription Plans</h1>
            <p class="text-muted">Manage pricing plans for your platform</p>
        </div>
        <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Plan
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($plans as $plan)
        <div class="col-md-4">
            <div class="card h-100 {{ $plan->is_popular ? 'border-primary' : '' }}">
                @if($plan->is_popular)
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-primary">Popular</span>
                    </div>
                @endif
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="card-title mb-1">{{ $plan->name }}</h5>
                            <p class="text-muted small mb-0">{{ $plan->description }}</p>
                        </div>
                        <div>
                            @if($plan->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h3 class="text-primary mb-0">{{ $plan->formatted_price }}</h3>
                        <small class="text-muted">per month</small>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted small mb-2">LIMITS</h6>
                        <div class="d-flex gap-3">
                            <div>
                                <small class="text-muted d-block">Accounts</small>
                                <strong>{{ $plan->limits['accounts'] }}</strong>
                            </div>
                            <div>
                                <small class="text-muted d-block">Users</small>
                                <strong>{{ $plan->limits['users'] }}</strong>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h6 class="text-muted small mb-2">FEATURES</h6>
                        <ul class="list-unstyled mb-0">
                            @foreach($plan->features as $feature)
                            <li class="mb-1">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                <small>{{ $feature }}</small>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <div class="card-footer bg-transparent">
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.subscription-plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary flex-fill">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.subscription-plans.toggle-status', $plan) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-{{ $plan->is_active ? 'eye-slash' : 'eye' }}"></i>
                                {{ $plan->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        <form action="{{ route('admin.subscription-plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">No subscription plans yet</h5>
                    <p class="text-muted">Create your first subscription plan to get started.</p>
                    <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i> Add New Plan
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
