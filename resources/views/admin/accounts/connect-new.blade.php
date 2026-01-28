@extends('admin.layout-new')

@section('title', 'Connect WhatsApp')
@section('page-title', 'Connect WhatsApp Account')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white text-center">
                    <h5 class="mb-0"><i class="bi bi-qr-code me-2"></i> Scan QR Code</h5>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <h6 class="text-muted">Account: {{ $account->name }}</h6>
                        <p class="text-muted mb-0">{{ $account->phone_number }}</p>
                    </div>

                    <div class="mb-4" id="qrCodeContainer">
                        @if($qrCode)
                        <img src="{{ $qrCode }}" alt="QR Code" class="img-fluid border rounded" style="max-width: 300px;">
                        @else
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="text-muted mt-3">Generating QR Code...</p>
                        @endif
                    </div>

                    <div id="statusBadge" class="mb-4">
                        @if($account->status == 'connected')
                        <span class="badge bg-success fs-6">
                            <i class="bi bi-check-circle me-2"></i> Connected
                        </span>
                        @else
                        <span class="badge bg-warning fs-6">
                            <i class="bi bi-clock me-2"></i> Waiting for connection...
                        </span>
                        @endif
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i> How to connect:</h6>
                        <ol class="text-start mb-0">
                            <li>Open WhatsApp on your phone</li>
                            <li>Tap <strong>Menu</strong> (⋮) or <strong>Settings</strong></li>
                            <li>Tap <strong>Linked Devices</strong></li>
                            <li>Tap <strong>Link a Device</strong></li>
                            <li>Point your phone at this screen to scan the QR code</li>
                        </ol>
                    </div>

                    <a href="{{ route('admin.accounts.show', $account) }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-2"></i> Back to Account
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-check connection status every 3 seconds
setInterval(function() {
    fetch('{{ route('admin.accounts.check-status', $account) }}')
        .then(response => response.json())
        .then(data => {
            const statusBadge = document.getElementById('statusBadge');
            
            if (data.connected) {
                statusBadge.innerHTML = '<span class="badge bg-success fs-6"><i class="bi bi-check-circle me-2"></i> Connected</span>';
                
                // Show success message and redirect
                setTimeout(function() {
                    window.location.href = '{{ route('admin.accounts.show', $account) }}';
                }, 2000);
            } else if (data.qr_code && data.qr_code !== '{{ $qrCode }}') {
                // Update QR code if changed
                document.getElementById('qrCodeContainer').innerHTML = 
                    '<img src="' + data.qr_code + '" alt="QR Code" class="img-fluid border rounded" style="max-width: 300px;">';
            }
        })
        .catch(error => console.error('Error checking status:', error));
}, 3000);
</script>
@endsection
