@extends('admin.layout')

@section('title', 'WhatsApp Accounts')
@section('page-title', 'WhatsApp Accounts')

@section('content')
<div class="flex justify-between mb-6">
    <h2 class="text-title-md2 font-semibold text-black dark:text-white">
        WhatsApp Accounts
    </h2>
    <a href="{{ route('admin.accounts.create') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-10 py-4 text-center font-medium text-white hover:bg-blue-700">
        <svg class="fill-current mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M10 0C4.48 0 0 4.48 0 10C0 15.52 4.48 20 10 20C15.52 20 20 15.52 20 10C20 4.48 15.52 0 10 0ZM15 11H11V15H9V11H5V9H9V5H11V9H15V11Z" fill=""/>
        </svg>
        Add Account
    </a>
</div>

<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="px-4 py-6 md:px-6 xl:px-7.5">
        <h4 class="text-xl font-semibold text-black dark:text-white">
            All Accounts ({{ $accounts->total() }})
        </h4>
    </div>

    <div class="grid grid-cols-6 border-t border-stroke px-4 py-4.5 dark:border-strokedark sm:grid-cols-8 md:px-6 2xl:px-7.5">
        <div class="col-span-2 flex items-center">
            <p class="font-medium text-black dark:text-white">Phone Number</p>
        </div>
        <div class="col-span-2 hidden items-center sm:flex">
            <p class="font-medium text-black dark:text-white">Name</p>
        </div>
        <div class="col-span-1 flex items-center">
            <p class="font-medium text-black dark:text-white">Status</p>
        </div>
        <div class="col-span-2 flex items-center">
            <p class="font-medium text-black dark:text-white">Last Connected</p>
        </div>
        <div class="col-span-1 flex items-center justify-end">
            <p class="font-medium text-black dark:text-white">Actions</p>
        </div>
    </div>

    @forelse($accounts as $account)
    <div class="grid grid-cols-6 border-t border-stroke px-4 py-4.5 dark:border-strokedark sm:grid-cols-8 md:px-6 2xl:px-7.5">
        <div class="col-span-2 flex items-center">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <p class="text-sm text-black dark:text-white">
                    {{ $account->phone_number }}
                </p>
            </div>
        </div>
        <div class="col-span-2 hidden items-center sm:flex">
            <p class="text-sm text-black dark:text-white">
                {{ $account->name ?? '-' }}
            </p>
        </div>
        <div class="col-span-1 flex items-center">
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
        <div class="col-span-2 flex items-center">
            <p class="text-sm text-black dark:text-white">
                {{ $account->last_connected_at ? $account->last_connected_at->diffForHumans() : 'Never' }}
            </p>
        </div>
        <div class="col-span-1 flex items-center justify-end gap-2">
            <a href="{{ route('admin.accounts.show', $account) }}" class="text-gray-600 hover:text-primary dark:text-gray-300 dark:hover:text-primary">
                <svg class="fill-current" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.99981 14.8219C3.43106 14.8219 0.674805 9.50624 0.562305 9.28124C0.47793 9.11249 0.47793 8.88749 0.562305 8.71874C0.674805 8.49374 3.43106 3.20624 8.99981 3.20624C14.5686 3.20624 17.3248 8.49374 17.4373 8.71874C17.5217 8.88749 17.5217 9.11249 17.4373 9.28124C17.3248 9.50624 14.5686 14.8219 8.99981 14.8219ZM1.85605 8.99999C2.4748 10.0406 4.89356 13.5562 8.99981 13.5562C13.1061 13.5562 15.5248 10.0406 16.1436 8.99999C15.5248 7.95936 13.1061 4.44374 8.99981 4.44374C4.89356 4.44374 2.4748 7.95936 1.85605 8.99999Z" fill=""/>
                    <path d="M9 11.3906C7.67812 11.3906 6.60938 10.3219 6.60938 9C6.60938 7.67813 7.67812 6.60938 9 6.60938C10.3219 6.60938 11.3906 7.67813 11.3906 9C11.3906 10.3219 10.3219 11.3906 9 11.3906ZM9 7.875C8.38125 7.875 7.875 8.38125 7.875 9C7.875 9.61875 8.38125 10.125 9 10.125C9.61875 10.125 10.125 9.61875 10.125 9C10.125 8.38125 9.61875 7.875 9 7.875Z" fill=""/>
                </svg>
            </a>
        </div>
    </div>
    @empty
    <div class="px-4 py-6 text-center">
        <p class="text-black dark:text-white">No accounts found</p>
    </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $accounts->links() }}
</div>
@endsection
