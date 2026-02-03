@extends('admin.layout-new')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">Welcome to WAIt</h2>
        </div>
    </div>

    <div class="row g-4">
        <!-- Total Accounts Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Accounts</h6>
                            <h3 class="mb-0">{{ \App\Models\WhatsappAccount::count() }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bi bi-phone fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connected Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Connected</h6>
                            <h3 class="mb-0 text-success">{{ \App\Models\WhatsappAccount::where('status', 'connected')->count() }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Messages Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Messages</h6>
                            <h3 class="mb-0">{{ \App\Models\WhatsappMessage::count() }}</h3>
                        </div>
                        <div class="text-info">
                            <i class="bi bi-envelope fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Sent Card -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Messages Sent</h6>
                            <h3 class="mb-0 text-success">{{ \App\Models\WhatsappMessage::where('status', 'sent')->count() }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="bi bi-send-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Quick Start Guide</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Go to <a href="{{ route('admin.accounts.index') }}">WhatsApp Accounts</a> and create a new account</li>
                        <li class="mb-2">Scan the QR code with your WhatsApp mobile app</li>
                        <li class="mb-2">Once connected, use your <code>sender_key</code> and <code>sender_secret</code> to send messages via API</li>
                        <li>Check the <a href="{{ route('admin.messages.index') }}">Messages</a> page to see sent messages</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
