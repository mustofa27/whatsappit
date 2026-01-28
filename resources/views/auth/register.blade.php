@extends('auth.layout')

@section('title', 'Register')

@section('content')
<div class="w-full max-w-md">
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white text-center text-2xl">
                Create Account
            </h3>
        </div>

        @if($errors->any())
        <div class="mx-6.5 mt-6 flex w-auto border-l-6 border-[#F87171] bg-[#F87171] bg-opacity-[15%] px-7 py-4 shadow-md dark:bg-[#1B1B24] dark:bg-opacity-30">
            <div class="w-full">
                <h5 class="mb-2 font-semibold text-[#B45454]">
                    Error
                </h5>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li class="leading-relaxed text-[#CD5D5D]">
                        {{ $error }}
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="p-6.5">
                <div class="mb-4.5">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Full Name
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        placeholder="Enter your full name"
                        class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 placeholder:text-gray-500 dark:placeholder:text-gray-400" />
                </div>

                <div class="mb-4.5">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        placeholder="Enter your email address"
                        class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 placeholder:text-gray-500 dark:placeholder:text-gray-400" />
                </div>

                <div class="mb-4.5">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Password
                    </label>
                    <input type="password" name="password" required
                        placeholder="Enter password (min. 8 characters)"
                        class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 placeholder:text-gray-500 dark:placeholder:text-gray-400" />
                </div>

                <div class="mb-6">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Confirm Password
                    </label>
                    <input type="password" name="password_confirmation" required
                        placeholder="Re-enter your password"
                        class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 placeholder:text-gray-500 dark:placeholder:text-gray-400" />
                </div>

                <button type="submit" class="flex w-full justify-center rounded bg-blue-600 p-3 font-medium text-white hover:bg-blue-700 transition">
                    Create Account
                </button>

                <div class="mt-6 text-center">
                    <p class="text-sm">
                        Already have an account?
                        <a href="{{ route('login') }}" class="text-primary hover:underline">Sign In</a>
                    </p>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
