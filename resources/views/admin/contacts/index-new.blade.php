@extends('admin.layout-new')

@section('title', 'Contacts')
@section('page-title', 'Contacts')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h4 mb-0">Contacts</h2>
        <a href="{{ route('admin.contacts.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add Contact
        </a>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.contacts.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Name, phone, email">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tag</label>
                    <input type="text" class="form-control" name="tag" value="{{ request('tag') }}" placeholder="VIP">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Account</label>
                    <select class="form-select" name="account_id">
                        <option value="">All</option>
                        @foreach($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ (string)request('account_id') === (string)$acc->id ? 'selected' : '' }}>
                                {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.contacts.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Contact List ({{ $contacts->total() }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Email</th>
                            <th>Tags</th>
                            <th>Account</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $contact)
                        <tr>
                            <td class="align-middle">{{ $contact->name ?? '-' }}</td>
                            <td class="align-middle">{{ $contact->contact_number }}</td>
                            <td class="align-middle">{{ $contact->email ?? '-' }}</td>
                            <td class="align-middle">
                                @if(!empty($contact->tags))
                                    @foreach($contact->tags as $tag)
                                        <span class="badge bg-info text-dark me-1">{{ $tag }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <small class="text-muted">{{ $contact->whatsappAccount->name ?? '-' }}</small>
                            </td>
                            <td class="align-middle text-end">
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('admin.contacts.edit', $contact) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this contact?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No contacts found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3">
        {{ $contacts->links() }}
    </div>
</div>
@endsection
