@extends('admin.layout')

@section('title', 'Account Details')
@section('page-title', 'Account Details')

@section('content')
<div class="grid grid-cols-1 gap-9">
    <!-- Account Info -->
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white">
                Account Information
            </h3>
        </div>
        <div class="p-7">
            <div class="mb-5.5 flex flex-col gap-5.5 sm:flex-row">
                <div class="w-full sm:w-1/2">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Phone Number
                    </label>
                    <div class="relative">
                        <span class="font-medium">{{ $account->phone_number }}</span>
                    </div>
                </div>

                <div class="w-full sm:w-1/2">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Account Name
                    </label>
                    <div class="relative">
                        <span class="font-medium">{{ $account->name ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="mb-5.5 flex flex-col gap-5.5 sm:flex-row">
                <div class="w-full sm:w-1/2">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Status
                    </label>
                    @if($account->status == 'connected')
                        <span class="inline-flex rounded-full bg-success bg-opacity-10 px-3 py-1 text-sm font-medium text-success">
                            Connected
                        </span>
                    @elseif($account->status == 'pending')
                        <span class="inline-flex rounded-full bg-warning bg-opacity-10 px-3 py-1 text-sm font-medium text-warning">
                            Pending
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-danger bg-opacity-10 px-3 py-1 text-sm font-medium text-danger">
                            {{ ucfirst($account->status) }}
                        </span>
                    @endif
                </div>

                <div class="w-full sm:w-1/2">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Last Connected
                    </label>
                    <span class="font-medium">{{ $account->last_connected_at ? $account->last_connected_at->format('M d, Y H:i') : 'Never' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- API Credentials -->
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white">
                API Credentials
            </h3>
        </div>
        <div class="p-7">
            <div class="mb-5.5">
                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                    Sender Key
                </label>
                <div class="relative">
                    <input type="text" readonly value="{{ $account->sender_key }}" class="w-full rounded border border-stroke bg-gray px-4.5 py-3 text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary" />
                    <button onclick="copyToClipboard('{{ $account->sender_key }}')" class="absolute right-4 top-1/2 -translate-y-1/2">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14 2H6C4.9 2 4 2.9 4 4V14C4 15.1 4.9 16 6 16H14C15.1 16 16 15.1 16 14V4C16 2.9 15.1 2 14 2ZM6 14H14V4H6V14ZM18 6V18C18 19.1 17.1 20 16 20H8V18H16V6H18Z" fill=""/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="mb-5.5">
                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                    Sender Secret
                </label>
                <div class="relative">
                    <input type="password" readonly value="{{ $account->sender_secret }}" id="sender_secret" class="w-full rounded border border-stroke bg-gray px-4.5 py-3 text-black focus:border-primary focus-visible:outline-none dark:border-strokedark dark:bg-meta-4 dark:text-white dark:focus:border-primary" />
                    <button onclick="togglePassword()" class="absolute right-14 top-1/2 -translate-y-1/2">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 3C5 3 1 10 1 10C1 10 5 17 10 17C15 17 19 10 19 10C19 10 15 3 10 3Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="10" cy="10" r="3" stroke="currentColor" stroke-width="2"/>
                        </svg>
                    </button>
                    <button onclick="copyToClipboard('{{ $account->sender_secret }}')" class="absolute right-4 top-1/2 -translate-y-1/2">
                        <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14 2H6C4.9 2 4 2.9 4 4V14C4 15.1 4.9 16 6 16H14C15.1 16 16 15.1 16 14V4C16 2.9 15.1 2 14 2ZM6 14H14V4H6V14ZM18 6V18C18 19.1 17.1 20 16 20H8V18H16V6H18Z" fill=""/>
                        </svg>
                    </button>
                </div>
            </div>

            <form action="{{ route('admin.accounts.regenerate', $account) }}" method="POST" onsubmit="return confirm('Are you sure? This will invalidate the current API keys!');">
                @csrf
                <button type="submit" class="flex justify-center rounded bg-warning px-6 py-2 font-medium text-white hover:bg-opacity-90">
                    Regenerate API Keys
                </button>
            </form>
        </div>
    </div>

    <!-- Actions -->
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-7 py-4 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white">
                Actions
            </h3>
        </div>
        <div class="p-7">
            <div class="flex gap-4">
                @if($account->status != 'connected')
                <a href="{{ route('admin.accounts.connect', $account) }}" class="flex justify-center rounded bg-blue-600 px-6 py-2 font-medium text-white hover:bg-blue-700">
                    <svg class="mr-2 fill-current" width="20" height="20" viewBox="0 0 20 20">
                        <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM10 18C5.59 18 2 14.41 2 10C2 5.59 5.59 2 10 2C14.41 2 18 5.59 18 10C18 14.41 14.41 18 10 18ZM13 10C13 11.66 11.66 13 10 13C8.34 13 7 11.66 7 10C7 8.34 8.34 7 10 7C11.66 7 13 8.34 13 10Z"/>
                    </svg>
                    Connect WhatsApp
                </a>
                @else
                <form action="{{ route('admin.accounts.initialize', $account) }}" method="POST">
                    @csrf
                    <button type="submit" class="flex justify-center rounded bg-warning px-6 py-2 font-medium text-white hover:bg-opacity-90">
                        Reconnect
                    </button>
                </form>
                @endif

                <a href="{{ route('admin.accounts.edit', $account) }}" class="flex justify-center rounded border border-stroke px-6 py-2 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                    Edit
                </a>

                <form action="{{ route('admin.accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this account?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex justify-center rounded bg-danger px-6 py-2 font-medium text-white hover:bg-opacity-90">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}

function togglePassword() {
    var x = document.getElementById("sender_secret");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}
</script>
@endsection
