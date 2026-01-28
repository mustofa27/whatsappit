@extends('admin.layout-new')

@section('title', 'WhatsApp Accounts')
@section('page-title', 'WhatsApp Accounts')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">WhatsApp Accounts</h2>
        <a href="{{ route('admin.accounts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i> Add Account
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">All Accounts ({{ $accounts->total() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Phone Number</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Last Connected</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        <tr>
                            <td class="align-middle">{{ $account->phone_number }}</td>
                            <td class="align-middle">{{ $account->name ?? '-' }}</td>
                            <td class="align-middle">
                                @if($account->status == 'connected')
                                    <span class="badge bg-success">Connected</span>
                                @elseif($account->status == 'connecting')
                                    <span class="badge bg-warning">Connecting</span>
                                @elseif($account->status == 'pending')
                                    <span class="badge bg-info">Pending</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($account->status) }}</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{ $account->last_connected_at ? $account->last_connected_at->diffForHumans() : 'Never' }}
                            </td>
                            <td class="align-middle text-end">
                                <a href="{{ route('admin.accounts.show', $account) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                No accounts found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $accounts->links() }}
    </div>
</div>
@endsection
