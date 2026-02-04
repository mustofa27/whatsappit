@extends('admin.layout-new')

@section('title', 'Message Templates')
@section('page-title', 'Message Templates')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Message Templates</h2>
        <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Create Template
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.templates.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Template name or content" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        <option value="MARKETING" {{ request('category') === 'MARKETING' ? 'selected' : '' }}>Marketing</option>
                        <option value="UTILITY" {{ request('category') === 'UTILITY' ? 'selected' : '' }}>Utility</option>
                        <option value="AUTHENTICATION" {{ request('category') === 'AUTHENTICATION' ? 'selected' : '' }}>Authentication</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Account</label>
                    <select name="account_id" class="form-select">
                        <option value="">All Accounts</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ request('account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-secondary">
                            <i class="bi bi-filter me-1"></i> Filter
                        </button>
                        <a href="{{ route('admin.templates.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Templates List -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">Templates ({{ $templates->total() }})</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Account</th>
                        <th>Status</th>
                        <th>Variables</th>
                        <th>Usage</th>
                        <th>Last Used</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $template)
                        <tr>
                            <td>
                                <strong>{{ $template->name }}</strong>
                                <br>
                                <small class="text-muted">{{ Str::limit($template->body_content, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $template->category_label }}</span>
                            </td>
                            <td>{{ $template->whatsappAccount->name }}</td>
                            <td>
                                <span class="badge {{ $template->status_badge }}">
                                    {{ ucfirst($template->status) }}
                                </span>
                            </td>
                            <td>
                                @if($template->variables && count($template->variables) > 0)
                                    <span class="badge bg-secondary">{{ count($template->variables) }} vars</span>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">{{ $template->usage_count }}</span>
                            </td>
                            <td>
                                @if($template->last_used_at)
                                    <small>{{ $template->last_used_at->diffForHumans() }}</small>
                                @else
                                    <small class="text-muted">Never</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.templates.show', $template) }}" class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($template->status === 'draft')
                                        <a href="{{ route('admin.templates.edit', $template) }}" class="btn btn-sm btn-outline-secondary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.templates.submit', $template) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success" title="Submit for Approval">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.templates.duplicate', $template) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-info" title="Duplicate">
                                            <i class="bi bi-files"></i>
                                        </button>
                                    </form>
                                    @if($template->status === 'draft')
                                        <form action="{{ route('admin.templates.destroy', $template) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete" onclick="return confirm('Delete this template?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-inbox display-1 text-muted"></i>
                                <p class="text-muted mt-2">No templates found</p>
                                <a href="{{ route('admin.templates.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle me-1"></i> Create Your First Template
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
            <div class="card-footer">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
