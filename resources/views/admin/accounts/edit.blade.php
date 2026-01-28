@extends('admin.layout')

@section('title', 'Edit Account')
@section('page-title', 'Edit WhatsApp Account')

@section('content')
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
        <h3 class="font-medium text-black dark:text-white">
            Edit WhatsApp Account
        </h3>
    </div>
    <form action="{{ route('admin.accounts.update', $account) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="p-6.5">
            <div class="mb-4.5">
                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                    Owner
                </label>
                <input type="text" value="{{ $account->user->name }} ({{ $account->user->email }})" readonly
                    class="w-full rounded border-2 border-gray-300 bg-gray-100 px-5 py-3 font-normal text-gray-600 cursor-not-allowed dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400" />
            </div>

            <div class="mb-4.5">
                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                    Phone Number <span class="text-meta-1">*</span>
                </label>
                <input type="text" name="phone_number" value="{{ old('phone_number', $account->phone_number) }}" required
                    class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400" />
            </div>

            <div class="mb-4.5">
                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                    Account Name
                </label>
                <input type="text" name="name" value="{{ old('name', $account->name) }}"
                    class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400" />
            </div>

            <div class="mb-6">
                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                    Status <span class="text-meta-1">*</span>
                </label>
                <select name="status" required class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400">
                    <option value="pending" {{ $account->status == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="connected" {{ $account->status == 'connected' ? 'selected' : '' }}>Connected</option>
                    <option value="disconnected" {{ $account->status == 'disconnected' ? 'selected' : '' }}>Disconnected</option>
                    <option value="failed" {{ $account->status == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex justify-center rounded bg-blue-600 px-6 py-2 font-medium text-white hover:bg-blue-700">
                    Update Account
                </button>
                <a href="{{ route('admin.accounts.show', $account) }}" class="flex justify-center rounded border border-stroke px-6 py-2 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
