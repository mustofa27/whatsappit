@extends('admin.layout-new')

@section('title', 'Pending Invitations')
@section('page-title', 'Pending Invitations')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0">Pending Team Invitations</h2>
            <small class="text-muted">
                @if($pendingInvitations->count() > 0)
                    {{ $pendingInvitations->total() }} pending invitation{{ $pendingInvitations->total() !== 1 ? 's' : '' }}
                @else
                    No pending invitations
                @endif
            </small>
        </div>
    </div>

    @if($pendingInvitations->count() > 0)
        <div class="row g-3">
            @foreach($pendingInvitations as $invitation)
                <div class="col-md-6">
                    <div class="card shadow-sm border-left-primary h-100" style="border-left: 4px solid #007bff;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <i class="bi bi-person-circle me-2"></i>{{ $invitation->owner->name }}
                                    </h5>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-envelope"></i> {{ $invitation->owner->email }}
                                    </p>
                                </div>
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock"></i> Pending
                                </span>
                            </div>

                            <hr class="my-3">

                            <div class="mb-3">
                                <p class="small text-muted mb-2">
                                    <strong>Role Offered:</strong>
                                </p>
                                @if($invitation->role === 'admin')
                                    <span class="badge bg-danger me-2">
                                        <i class="bi bi-shield-check"></i> Admin
                                    </span>
                                    <small class="text-muted d-block mt-1">Full access to team and WhatsApp account management</small>
                                @elseif($invitation->role === 'operator')
                                    <span class="badge bg-info me-2">
                                        <i class="bi bi-gear"></i> Operator
                                    </span>
                                    <small class="text-muted d-block mt-1">Can manage WhatsApp messages and conversations</small>
                                @else
                                    <span class="badge bg-secondary me-2">
                                        <i class="bi bi-eye"></i> Viewer
                                    </span>
                                    <small class="text-muted d-block mt-1">Read-only access to team data and reports</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <p class="small text-muted mb-2">
                                    <strong>Expires:</strong>
                                </p>
                                @if($invitation->isInvitationValid())
                                    <small class="text-muted">
                                        <i class="bi bi-calendar-event"></i>
                                        {{ $invitation->invite_expires_at->format('M d, Y') }}
                                        @if($invitation->invite_expires_at->diffInDays(now()) < 2)
                                            <span class="text-danger ms-2"><strong>(Expiring soon!)</strong></span>
                                        @else
                                            <span class="text-muted ms-2">({{ $invitation->invite_expires_at->diffForHumans() }})</span>
                                        @endif
                                    </small>
                                @else
                                    <small class="text-danger">
                                        <i class="bi bi-exclamation-circle"></i> This invitation has expired
                                    </small>
                                @endif
                            </div>

                            <div class="d-flex gap-2 mt-4">
                                @if($invitation->isInvitationValid())
                                    <form 
                                        action="{{ route('team-members.accept', ['token' => $invitation->invite_token]) }}" 
                                        method="POST" 
                                        style="flex: 1;"
                                        onsubmit="return confirm('Accept this team invitation?');"
                                    >
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="bi bi-check-circle me-2"></i> Accept
                                        </button>
                                    </form>

                                    <form 
                                        action="{{ route('team-members.reject', ['token' => $invitation->invite_token]) }}" 
                                        method="POST" 
                                        style="flex: 1;"
                                        onsubmit="return confirm('Reject this team invitation?');"
                                    >
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger w-100">
                                            <i class="bi bi-x-circle me-2"></i> Reject
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-danger w-100 mb-0">
                                        <small>This invitation has expired. Please ask {{ $invitation->owner->name }} to send a new invitation.</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($pendingInvitations->hasPages())
            <div class="mt-4">
                {{ $pendingInvitations->links() }}
            </div>
        @endif
    @else
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                <h5 class="mt-3 text-muted">No Pending Invitations</h5>
                <p class="text-muted small">
                    You don't have any pending team invitations at the moment.
                </p>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary btn-sm mt-3">
                    <i class="bi bi-house me-2"></i> Go to Dashboard
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

