@extends('admin.layout-new')

@section('title', 'Invite Team Member')
@section('page-title', 'Invite Team Member')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mx-auto">
            @if(!auth()->user()->canAddTeamMember())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Team Member Limit Reached</strong> - You've reached your team member limit ({{ $maxMembers - 1 }}).
                    <a href="{{ route('subscription.index') }}" class="alert-link">Upgrade your subscription</a> to add more members.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-lock-fill" style="font-size: 3rem; color: #dc3545;"></i>
                        <h5 class="mt-3 mb-2">Invitation Disabled</h5>
                        <p class="text-muted mb-3">You've reached your maximum number of team members.</p>
                        <p class="mb-4">
                            <strong>Current Usage:</strong> {{ $memberCount }} of {{ $maxMembers - 1 }} members
                        </p>
                        <a href="{{ route('subscription.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-up me-2"></i> Upgrade Subscription
                        </a>
                    </div>
                </div>
            @else
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0">Invite Team Member</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Current Usage: {{ $memberCount }} of {{ $maxMembers - 1 }} members
                            @if($remainingSlots > 0)
                                <span class="ms-2">({{ $remainingSlots }} slot{{ $remainingSlots > 1 ? 's' : '' }} remaining)</span>
                            @endif
                        </div>

                        <form action="{{ route('admin.team-members.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                                <small class="text-muted d-block mt-2">
                                    Enter the email address of the person you want to invite to your team.
                                </small>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select @error('role') is-invalid @enderror" 
                                        id="role" name="role" required>
                                    <option value="">Select a role...</option>
                                    <option value="admin" @if(old('role') === 'admin') selected @endif>
                                        Admin - Full access to all features
                                    </option>
                                    <option value="operator" @if(old('role') === 'operator') selected @endif>
                                        Operator - Can manage accounts and send messages
                                    </option>
                                    <option value="viewer" @if(old('role') === 'viewer') selected @endif>
                                        Viewer - Read-only access
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.team-members.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-1"></i> Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-1"></i> Send Invitation
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="fw-bold mb-2"><i class="bi bi-question-circle me-2"></i>About Roles</h6>
                        <ul class="mb-0 small">
                            <li><strong>Admin:</strong> Can manage team members, settings, and all features</li>
                            <li><strong>Operator:</strong> Can manage WhatsApp accounts and send messages</li>
                            <li><strong>Viewer:</strong> Can only view reports and analytics</li>
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
