@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="mb-0">
                <i class="bi bi-inbox"></i> Pending Team Invitations
            </h1>
            <p class="text-muted small">Review and respond to invitations to join other users' WhatsApp teams</p>
        </div>
    </div>

    @if($pendingInvitations->count() > 0)
        <div class="row">
            @foreach($pendingInvitations as $invitation)
                <div class="col-md-6 mb-3">
                    <div class="card border-left-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        {{ $invitation->owner->name }}
                                    </h5>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-envelope"></i> {{ $invitation->owner->email }}
                                    </p>
                                </div>
                                <span class="badge bg-warning text-dark">Pending</span>
                            </div>

                            <div class="mb-3">
                                <p class="small mb-2">
                                    <strong>Role Offered:</strong>
                                </p>
                                <div class="role-badge">
                                    @if($invitation->role === 'admin')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-shield-check"></i> Admin
                                        </span>
                                        <p class="small text-muted mt-1">Full access to team and WhatsApp account management</p>
                                    @elseif($invitation->role === 'operator')
                                        <span class="badge bg-info">
                                            <i class="bi bi-gear"></i> Operator
                                        </span>
                                        <p class="small text-muted mt-1">Can manage WhatsApp messages and conversations</p>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-eye"></i> Viewer
                                        </span>
                                        <p class="small text-muted mt-1">Read-only access to team data and reports</p>
                                    @endif
                                </div>
                            </div>

                            <div class="alert alert-info small mb-3">
                                <i class="bi bi-clock"></i>
                                Expires on {{ $invitation->invite_expires_at->format('M d, Y') }}
                                @if($invitation->invite_expires_at->diffInDays(now()) < 2)
                                    <span class="text-danger"><strong>(Expiring soon!)</strong></span>
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <form 
                                    action="{{ route('team-members.accept', ['token' => $invitation->invite_token]) }}" 
                                    method="POST" 
                                    style="display: inline;"
                                    onsubmit="return confirm('Accept this team invitation?');"
                                >
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="bi bi-check-circle"></i> Accept
                                    </button>
                                </form>

                                <form 
                                    action="{{ route('team-members.reject', ['token' => $invitation->invite_token]) }}" 
                                    method="POST" 
                                    style="display: inline;"
                                    onsubmit="return confirm('Reject this team invitation?');"
                                >
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button>
                                </form>
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
        <div class="alert alert-info text-center py-5">
            <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.5;"></i>
            <p class="mt-3 mb-0">
                <strong>No pending invitations</strong><br>
                <small class="text-muted">You don't have any pending team invitations at the moment</small>
            </p>
        </div>
    @endif
</div>

<style>
    .card.border-left-primary {
        border-left: 4px solid #007bff !important;
    }

    .role-badge {
        display: inline-block;
    }
</style>
@endsection
