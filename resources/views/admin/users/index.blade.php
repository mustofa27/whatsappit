@extends('admin.layout-new')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0">Users</h2>
            <small class="text-muted">{{ $users->total() }} total users</small>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus me-2"></i> Add User
        </a>
    </div>

    <!-- Search and Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name or email" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="admin" class="form-select">
                        <option value="">All Users</option>
                        <option value="yes" @if(request('admin') == 'yes') selected @endif>Admins Only</option>
                        <option value="no" @if(request('admin') == 'no') selected @endif>Non-Admins Only</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none">
                                    Name
                                    @if(request('sort') == 'name')
                                        <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none">
                                    Email
                                    @if(request('sort') == 'email')
                                        <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>Role</th>
                            <th>Subscription</th>
                            <th>
                                <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('direction') == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none">
                                    Joined
                                    @if(request('sort') == 'created_at')
                                        <i class="bi bi-arrow-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="align-middle">
                                    <strong>{{ $user->name }}</strong>
                                </td>
                                <td class="align-middle">{{ $user->email }}</td>
                                <td class="align-middle">
                                    @if($user->is_admin)
                                        <span class="badge bg-danger">
                                            <i class="bi bi-shield-check"></i> Admin
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">User</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    @if($user->hasActiveSubscription())
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle"></i> Active
                                        </span>
                                    @else
                                        <span class="badge bg-warning">None</span>
                                    @endif
                                </td>
                                <td class="align-middle">{{ $user->created_at->format('d M Y') }}</td>
                                <td class="align-middle text-end">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    No users found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection
