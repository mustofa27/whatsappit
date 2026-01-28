@extends('admin.layout')

@section('title', 'Create Account')
@section('page-title', 'Create WhatsApp Account')

@section('content')
<div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
    <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
        <h3 class="font-medium text-black dark:text-white">
            Create New WhatsApp Account
        </h3>
    </div>
    <form action="{{ route('admin.accounts.store') }}" method="POST">
        @csrf
        <div class="p-6.5">
            <div class="mb-4.5">
                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                    Phone Number <span class="text-meta-1">*</span>
                </label>
                <input type="text" name="phone_number" value="{{ old('phone_number') }}" required
                    placeholder="628123456789"
                    class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 placeholder:text-gray-500 dark:placeholder:text-gray-400" />
                @error('phone_number')
                    <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-bodydark">Format: 628123456789 (Indonesia country code)</p>
            </div>

            <div class="mb-6">
                <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                    Account Name (Optional)
                </label>
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="My Business Account"
                    class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 placeholder:text-gray-500 dark:placeholder:text-gray-400" />
                @error('name')
                    <p class="mt-1 text-sm text-meta-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex justify-center rounded bg-blue-600 px-6 py-2 font-medium text-white hover:bg-blue-700">
                    Create Account
                </button>
                <a href="{{ route('admin.accounts.index') }}" class="flex justify-center rounded border border-stroke px-6 py-2 font-medium text-black hover:shadow-1 dark:border-strokedark dark:text-white">
                    Cancel
                </a>
            </div>
        </div>
    </form>
</div>
@endsection
