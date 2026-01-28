@extends('admin.layout')

@section('title', 'Messages')
@section('page-title', 'WhatsApp Messages')

@section('content')
<div class="mb-6">
    <h2 class="text-title-md2 font-semibold text-black dark:text-white">
        Messages History
    </h2>
</div>

<!-- Filters -->
<div class="mb-6 rounded-sm border border-stroke bg-white px-5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
    <form method="GET" action="{{ route('admin.messages.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div>
            <label class="mb-2 block text-sm font-medium text-black dark:text-white">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Phone or message..."
                class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white" />
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-black dark:text-white">Status</label>
            <select name="status" class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white">
                <option value="">All</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="sent" {{ request('status') == 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-black dark:text-white">Account</label>
            <select name="account_id" class="w-full rounded border-[1.5px] border-stroke bg-transparent px-5 py-3 font-normal text-black outline-none transition focus:border-primary active:border-primary disabled:cursor-default disabled:bg-whiter dark:border-form-strokedark dark:bg-form-input dark:text-white">
                <option value="">All Accounts</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                    {{ $acc->name ?? $acc->phone_number }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button type="submit" class="rounded bg-primary px-6 py-3 font-medium text-white hover:bg-opacity-90">
                Filter
            </button>
        </div>
    </form>
</div>

<!-- Messages Table -->
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="px-4 py-6 md:px-6 xl:px-7.5">
        <h4 class="text-xl font-semibold text-black dark:text-white">
            All Messages ({{ $messages->total() }})
        </h4>
    </div>

    <div class="grid grid-cols-6 border-t border-stroke px-4 py-4.5 dark:border-strokedark md:px-6 2xl:px-7.5">
        <div class="col-span-1">
            <p class="font-medium">To</p>
        </div>
        <div class="col-span-2">
            <p class="font-medium">Message</p>
        </div>
        <div class="col-span-1">
            <p class="font-medium">Account</p>
        </div>
        <div class="col-span-1">
            <p class="font-medium">Status</p>
        </div>
        <div class="col-span-1">
            <p class="font-medium">Time</p>
        </div>
    </div>

    @forelse($messages as $message)
    <div class="grid grid-cols-6 border-t border-stroke px-4 py-4.5 dark:border-strokedark md:px-6 2xl:px-7.5">
        <div class="col-span-1 flex items-center">
            <p class="text-sm text-black dark:text-white">{{ $message->recipient_number }}</p>
        </div>
        <div class="col-span-2 flex items-center">
            <p class="text-sm text-black dark:text-white truncate">{{ Str::limit($message->message, 50) }}</p>
        </div>
        <div class="col-span-1 flex items-center">
            <p class="text-sm text-black dark:text-white">{{ $message->whatsappAccount->name ?? $message->whatsappAccount->phone_number }}</p>
        </div>
        <div class="col-span-1 flex items-center">
            @if($message->status == 'sent' || $message->status == 'delivered' || $message->status == 'read')
                <span class="inline-flex rounded-full bg-success bg-opacity-10 px-3 py-1 text-sm font-medium text-success">
                    {{ ucfirst($message->status) }}
                </span>
            @elseif($message->status == 'pending')
                <span class="inline-flex rounded-full bg-warning bg-opacity-10 px-3 py-1 text-sm font-medium text-warning">
                    Pending
                </span>
            @else
                <span class="inline-flex rounded-full bg-danger bg-opacity-10 px-3 py-1 text-sm font-medium text-danger">
                    Failed
                </span>
            @endif
        </div>
        <div class="col-span-1 flex items-center">
            <p class="text-sm text-black dark:text-white">{{ $message->created_at->format('M d, H:i') }}</p>
        </div>
    </div>
    @empty
    <div class="px-4 py-6 text-center">
        <p class="text-black dark:text-white">No messages found</p>
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $messages->links() }}
</div>
@endsection
