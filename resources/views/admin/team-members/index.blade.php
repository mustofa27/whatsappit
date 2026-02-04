@extends('admin.layout-new')

@section('title', 'Team Members')
@section('page-title', 'Team Members')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0">Team Members</h2>
            <small class="text-muted">
                {{ $memberCount }} of {{ $maxMembers - 1 }} team members
            </small>
        </div>
        @if($canManageTeamMembers)
            @if($canAddMember)
                <a href="{{ route('admin.team-members.create') }}" class="btn btn-primary">
                    <i class="bi bi-person-plus me-2"></i> Invite Member
                </a>
            @else
                <button class="btn btn-secondary" disabled title="Team member limit reached">
                    <i class="bi bi-person-plus me-2"></i> Invite Member
                </button>
            @endif
        @endif
    </div>

    @if(!$canManageTeamMembers)
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="bi bi-info-circle me-2"></i>
            Only the team owner can invite, cancel, or manage members.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(!$canAddMember)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Team Member Limit Reached</strong> - You've reached your team member limit ({{ $maxMembers - 1 }}).
            <a href="{{ route('subscription.index') }}" class="alert-link">Upgrade your subscription</a> to add more members.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($pendingInvitations->count() > 0)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">
                    <i class="bi bi-clock-history me-2"></i>Pending Invitations
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Invited</th>
                                <th>Expires</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pendingInvitations as $invitation)
                            <tr>
                                <td class="align-middle">{{ $invitation->user->email }}</td>
                                <td class="align-middle">
                                    <span class="badge bg-info">{{ ucfirst($invitation->role) }}</span>
                                </td>
                                <td class="align-middle">
                                    {{ $invitation->created_at->diffForHumans() }}
                                </td>
                                <td class="align-middle">
                                    @if($invitation->isInvitationValid())
                                        <small class="text-muted">{{ $invitation->invite_expires_at->diffForHumans() }}</small>
                                    @else
                                        <small class="text-danger">Expired</small>
                                    @endif
                                </td>
                                <td class="align-middle text-end">
                                    @if($canManageTeamMembers)
                                        <form action="{{ route('admin.team-members.destroy', $invitation) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel this invitation?')">
                                                <i class="bi bi-trash"></i> Cancel
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">No actions</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="bi bi-people-fill me-2"></i>Active Members
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeMembers as $member)
                        <tr>
                            <td class="align-middle">{{ $member->user->name }}</td>
                            <td class="align-middle">{{ $member->user->email }}</td>
                            <td class="align-middle">
                                <form action="{{ route('admin.team-members.update', $member) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <select name="role" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                        <option value="admin" @if($member->role === 'admin') selected @endif>Admin</option>
                                        <option value="operator" @if($member->role === 'operator') selected @endif>Operator</option>
                                        <option value="viewer" @if($member->role === 'viewer') selected @endif>Viewer</option>
                                    </select>
                                </form>
                            </td>
                            <td class="align-middle">
                                {{ $member->accepted_at->diffForHumans() }}
                            </td>
                            <td class="align-middle text-end">
                                <form action="{{ route('admin.team-members.destroy', $member) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this member?')">
                                        <i class="bi bi-trash"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No team members yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($activeMembers->hasPages())
        <div class="mt-3">
            {{ $activeMembers->links() }}
        </div>
    @endif
</div>
@endsection
