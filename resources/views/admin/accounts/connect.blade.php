@extends('admin.layout')

@section('title', 'Connect WhatsApp')
@section('page-title', 'Connect WhatsApp Account')

@section('content')
<div class="grid grid-cols-1 gap-9">
    <!-- QR Code Section -->
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white">
                Scan QR Code to Connect
            </h3>
        </div>
        <div class="p-7">
            <div class="mb-6">
                <div class="flex flex-col items-center justify-center">
                    <!-- Status Badge -->
                    <div id="status-badge" class="mb-4">
                        @if($account->status == 'connected')
                            <span class="inline-flex rounded-full bg-success bg-opacity-10 px-4 py-2 text-base font-medium text-success">
                                <svg class="mr-2 fill-current" width="20" height="20" viewBox="0 0 20 20">
                                    <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z"/>
                                </svg>
                                Connected
                            </span>
                        @elseif($account->status == 'connecting')
                            <span class="inline-flex rounded-full bg-warning bg-opacity-10 px-4 py-2 text-base font-medium text-warning">
                                <svg class="mr-2 fill-current animate-spin" width="20" height="20" viewBox="0 0 20 20">
                                    <path d="M10 3V0L6 4L10 8V5C13.31 5 16 7.69 16 11C16 12.01 15.75 12.97 15.3 13.8L16.74 15.24C17.55 14.1 18 12.61 18 11C18 6.58 14.42 3 10 3Z"/>
                                </svg>
                                Connecting...
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-danger bg-opacity-10 px-4 py-2 text-base font-medium text-danger">
                                Disconnected
                            </span>
                        @endif
                    </div>

                    <!-- QR Code Container -->
                    <div id="qr-container" class="p-8 bg-white border-2 border-gray-200 rounded-lg dark:bg-gray-800 dark:border-gray-700">
                        @if($account->status == 'connected')
                            <div class="text-center">
                                <svg class="mx-auto mb-4 fill-success" width="80" height="80" viewBox="0 0 80 80">
                                    <path d="M40 0C17.92 0 0 17.92 0 40C0 62.08 17.92 80 40 80C62.08 80 80 62.08 80 40C80 17.92 62.08 0 40 0ZM32 60L12 40L16.84 35.16L32 50.32L63.16 19.16L68 24L32 60Z"/>
                                </svg>
                                <p class="text-xl font-semibold text-success">Successfully Connected!</p>
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Your WhatsApp account is now connected.</p>
                            </div>
                        @else
                            <img id="qr-image" src="{{ $qrCode }}" alt="QR Code" class="w-64 h-64">
                        @endif
                    </div>

                    <!-- Instructions -->
                    <div id="instructions" class="mt-6 max-w-md text-center {{ $account->status == 'connected' ? 'hidden' : '' }}">
                        <h4 class="mb-3 text-lg font-semibold text-black dark:text-white">How to Connect:</h4>
                        <ol class="text-left list-decimal list-inside space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li>Open WhatsApp on your phone</li>
                            <li>Tap <strong>Menu</strong> or <strong>Settings</strong> and select <strong>Linked Devices</strong></li>
                            <li>Tap on <strong>Link a Device</strong></li>
                            <li>Point your phone at this screen to scan the QR code</li>
                        </ol>
                        
                        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded dark:bg-blue-900/20 dark:border-blue-800">
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                <strong>Note:</strong> The QR code will refresh automatically. Keep this page open until connected.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="mt-8 p-4 bg-gray-50 rounded dark:bg-gray-900">
                <h5 class="mb-3 text-sm font-semibold text-black dark:text-white">Account Details:</h5>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Phone Number:</span>
                        <span class="ml-2 font-medium text-black dark:text-white">{{ $account->phone_number }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600 dark:text-gray-400">Account Name:</span>
                        <span class="ml-2 font-medium text-black dark:text-white">{{ $account->name }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-4 justify-center">
                @if($account->status == 'connected')
                    <a href="{{ route('admin.accounts.show', $account) }}" class="flex justify-center rounded bg-blue-600 px-6 py-2 font-medium text-white hover:bg-blue-700">
                        View Account Details
                    </a>
                    <a href="{{ route('admin.accounts.index') }}" class="flex justify-center rounded border border-stroke px-6 py-2 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                        Back to Accounts
                    </a>
                @else
                    <button onclick="refreshQRCode()" class="flex justify-center rounded bg-warning px-6 py-2 font-medium text-white hover:bg-opacity-90">
                        Refresh QR Code
                    </button>
                    <a href="{{ route('admin.accounts.index') }}" class="flex justify-center rounded border border-stroke px-6 py-2 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                        Skip for Now
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
let checkInterval;
const accountId = {{ $account->id }};

// Auto-check connection status every 3 seconds
function startStatusCheck() {
    checkInterval = setInterval(checkConnectionStatus, 3000);
}

function stopStatusCheck() {
    if (checkInterval) {
        clearInterval(checkInterval);
    }
}

async function checkConnectionStatus() {
    try {
        const response = await fetch(`/admin/accounts/${accountId}/check-status`);
        const data = await response.json();
        
        updateStatus(data.status, data.connected);
        
        // Stop checking if connected
        if (data.connected) {
            stopStatusCheck();
        }
    } catch (error) {
        console.error('Error checking status:', error);
    }
}

function updateStatus(status, connected) {
    const statusBadge = document.getElementById('status-badge');
    const qrContainer = document.getElementById('qr-container');
    const instructions = document.getElementById('instructions');
    
    if (connected) {
        // Show success state
        statusBadge.innerHTML = `
            <span class="inline-flex rounded-full bg-success bg-opacity-10 px-4 py-2 text-base font-medium text-success">
                <svg class="mr-2 fill-current" width="20" height="20" viewBox="0 0 20 20">
                    <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM8 15L3 10L4.41 8.59L8 12.17L15.59 4.58L17 6L8 15Z"/>
                </svg>
                Connected
            </span>
        `;
        
        qrContainer.innerHTML = `
            <div class="text-center">
                <svg class="mx-auto mb-4 fill-success" width="80" height="80" viewBox="0 0 80 80">
                    <path d="M40 0C17.92 0 0 17.92 0 40C0 62.08 17.92 80 40 80C62.08 80 80 62.08 80 40C80 17.92 62.08 0 40 0ZM32 60L12 40L16.84 35.16L32 50.32L63.16 19.16L68 24L32 60Z"/>
                </svg>
                <p class="text-xl font-semibold text-success">Successfully Connected!</p>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Your WhatsApp account is now connected.</p>
            </div>
        `;
        
        instructions.classList.add('hidden');
        
        // Reload page after 2 seconds to show updated UI
        setTimeout(() => {
            window.location.reload();
        }, 2000);
    }
}

function refreshQRCode() {
    window.location.reload();
}

// Start checking when page loads
@if($account->status != 'connected')
    startStatusCheck();
@endif

// Clean up when leaving page
window.addEventListener('beforeunload', () => {
    stopStatusCheck();
});
</script>
@endsection
