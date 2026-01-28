@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('breadcrumb')
<nav class="mb-6">
    <ol class="flex items-center gap-2">
        <li>
            <a class="font-medium" href="{{ route('admin.dashboard') }}">Dashboard</a>
        </li>
    </ol>
</nav>
@endsection

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 gap-4 md:grid-cols-2 md:gap-6 xl:grid-cols-4 2xl:gap-7.5">
    <!-- Card 1 -->
    <div class="rounded-sm border border-stroke bg-white px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 0C7.41 0 4.5 2.91 4.5 6.5C4.5 10.09 7.41 13 11 13C14.59 13 17.5 10.09 17.5 6.5C17.5 2.91 14.59 0 11 0ZM11 11C8.52 11 6.5 8.98 6.5 6.5C6.5 4.02 8.52 2 11 2C13.48 2 15.5 4.02 15.5 6.5C15.5 8.98 13.48 11 11 11ZM17.04 13.81C15.2 13.29 13.27 13 11.25 13C9.23 13 7.3 13.29 5.46 13.81C2.61 14.67 0.5 17.4 0.5 20.5V22H22V20.5C22 17.4 19.89 14.67 17.04 13.81Z" fill=""/>
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">
                    {{ $stats['total_accounts'] }}
                </h4>
                <span class="text-sm font-medium">Total Accounts</span>
            </div>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="rounded-sm border border-stroke bg-white px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 0C4.93 0 0 4.93 0 11C0 17.07 4.93 22 11 22C17.07 22 22 17.07 22 11C22 4.93 17.07 0 11 0ZM11 20C6.04 20 2 15.96 2 11C2 6.04 6.04 2 11 2C15.96 2 20 6.04 20 11C20 15.96 15.96 20 11 20Z" fill=""/>
                <path d="M15.59 7.58L10 13.17L6.41 9.59L5 11L10 16L17 9L15.59 7.58Z" fill=""/>
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">
                    {{ $stats['connected_accounts'] }}
                </h4>
                <span class="text-sm font-medium">Connected</span>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="rounded-sm border border-stroke bg-white px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2H4C2.9 2 2.01 2.9 2.01 4L2 22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill=""/>
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">
                    {{ $stats['total_messages'] }}
                </h4>
                <span class="text-sm font-medium">Total Messages</span>
            </div>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="rounded-sm border border-stroke bg-white px-7.5 py-6 shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="flex h-11.5 w-11.5 items-center justify-center rounded-full bg-meta-2 dark:bg-meta-4">
            <svg class="fill-primary dark:fill-white" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M20 2H4C2.9 2 2.01 2.9 2.01 4L2 22L6 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM20 16H6L4 18V4H20V16Z" fill=""/>
                <path d="M7 9H17V11H7V9ZM7 6H17V8H7V6ZM7 12H14V14H7V12Z" fill=""/>
            </svg>
        </div>
        <div class="mt-4 flex items-end justify-between">
            <div>
                <h4 class="text-title-md font-bold text-black dark:text-white">
                    {{ $stats['messages_today'] }}
                </h4>
                <span class="text-sm font-medium">Today</span>
            </div>
        </div>
    </div>
</div>

<!-- Recent Messages Table -->
<div class="mt-7.5 grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
    <div class="col-span-12">
        <div class="rounded-sm border border-stroke bg-white px-5 pb-2.5 pt-6 shadow-default dark:border-strokedark dark:bg-boxdark sm:px-7.5 xl:pb-1">
            <div class="mb-6 flex justify-between">
                <div>
                    <h4 class="text-xl font-semibold text-black dark:text-white">
                        Recent Messages
                    </h4>
                </div>
                <div>
                    <a href="{{ route('admin.messages.index') }}" class="inline-flex items-center justify-center rounded-md bg-primary px-10 py-4 text-center font-medium text-white hover:bg-opacity-90 lg:px-8 xl:px-10">
                        View All
                    </a>
                </div>
            </div>

            <div class="flex flex-col">
                <div class="grid grid-cols-3 rounded-sm bg-gray-2 dark:bg-meta-4 sm:grid-cols-5">
                    <div class="p-2.5 xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">To</h5>
                    </div>
                    <div class="p-2.5 text-center xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">Message</h5>
                    </div>
                    <div class="p-2.5 text-center xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">Status</h5>
                    </div>
                    <div class="hidden p-2.5 text-center sm:block xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">Account</h5>
                    </div>
                    <div class="hidden p-2.5 text-center sm:block xl:p-5">
                        <h5 class="text-sm font-medium uppercase xsm:text-base">Time</h5>
                    </div>
                </div>

                @forelse($recent_messages as $message)
                <div class="grid grid-cols-3 border-b border-stroke dark:border-strokedark sm:grid-cols-5">
                    <div class="flex items-center gap-3 p-2.5 xl:p-5">
                        <p class="text-black dark:text-white">{{ $message->recipient_number }}</p>
                    </div>
                    <div class="flex items-center justify-center p-2.5 xl:p-5">
                        <p class="text-black dark:text-white truncate max-w-xs">{{ Str::limit($message->message, 30) }}</p>
                    </div>
                    <div class="flex items-center justify-center p-2.5 xl:p-5">
                        @if($message->status == 'sent')
                            <span class="inline-flex rounded-full bg-success bg-opacity-10 px-3 py-1 text-sm font-medium text-success">
                                Sent
                            </span>
                        @elseif($message->status == 'pending')
                            <span class="inline-flex rounded-full bg-warning bg-opacity-10 px-3 py-1 text-sm font-medium text-warning">
                                Pending
                            </span>
                        @else
                            <span class="inline-flex rounded-full bg-danger bg-opacity-10 px-3 py-1 text-sm font-medium text-danger">
                                {{ ucfirst($message->status) }}
                            </span>
                        @endif
                    </div>
                    <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                        <p class="text-black dark:text-white">{{ $message->whatsappAccount->name ?? $message->whatsappAccount->phone_number }}</p>
                    </div>
                    <div class="hidden items-center justify-center p-2.5 sm:flex xl:p-5">
                        <p class="text-black dark:text-white text-sm">{{ $message->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <div class="p-5 text-center">
                    <p class="text-black dark:text-white">No messages yet</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
