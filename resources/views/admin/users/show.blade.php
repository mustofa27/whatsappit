@extends('admin.layout-new')

@section('title', $user->name)
@section('page-title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0">{{ $user->name }}</h2>
            <small class="text-muted">{{ $user->email }}</small>
        </div>
        <div>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this user?')">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- User Info -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>User Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Name</label>
                            <p class="mb-0"><strong>{{ $user->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email</label>
                            <p class="mb-0"><strong>{{ $user->email }}</strong></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Role</label>
                            <p class="mb-0">
                                @if($user->is_admin)
                                    <span class="badge bg-danger"><i class="bi bi-shield-check"></i> Admin</span>
                                @else
                                    <span class="badge bg-secondary">User</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Member Since</label>
                            <p class="mb-0"><strong>{{ $user->created_at->format('d M Y') }}</strong></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <label class="text-muted small">Last Updated</label>
                            <p class="mb-0"><strong>{{ $user->updated_at->diffForHumans() }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Info -->
        <div class="col-lg-6">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Subscription</h5>
                </div>
                <div class="card-body">
                    @if($activeSubscription)
                        <div class="mb-3">
                            <label class="text-muted small">Plan</label>
                            <p class="mb-0"><strong>{{ $activeSubscription->plan->name }}</strong></p>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted small">Status</label>
                                <p class="mb-0">
                                    @if($activeSubscription->status === 'active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-warning">{{ ucfirst($activeSubscription->status) }}</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small">Expires</label>
                                <p class="mb-0"><strong>{{ $activeSubscription->expires_at->format('d M Y') }}</strong></p>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">No active subscription</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow-sm border-left-primary" style="border-left: 4px solid #007bff;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">WhatsApp Accounts</h6>
                            <h3 class="mb-0">{{ $accounts }}</h3>
                        </div>
                        <div class="text-primary fs-1 opacity-25">
                            <i class="bi bi-phone"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-left-success" style="border-left: 4px solid #198754;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Team Owner</h6>
                            <h3 class="mb-0">{{ $teamOwned }}</h3>
                        </div>
                        <div class="text-success fs-1 opacity-25">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <small class="text-muted">members managed</small>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-left-info" style="border-left: 4px solid #0d6efd;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Team Member</h6>
                            <h3 class="mb-0">{{ $teamMember }}</h3>
                        </div>
                        <div class="text-info fs-1 opacity-25">
                            <i class="bi bi-person-badge"></i>
                        </div>
                    </div>
                    <small class="text-muted">teams joined</small>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Users
        </a>
    </div>
</div>
@endsection
