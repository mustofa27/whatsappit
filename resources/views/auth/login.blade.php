@extends('auth.layout')

@section('title', 'Login')

@section('content')
<div class="w-full max-w-md">
    <div class="rounded-sm border border-stroke bg-white shadow-default dark:border-strokedark dark:bg-boxdark">
        <div class="border-b border-stroke px-6.5 py-4 dark:border-strokedark">
            <h3 class="font-medium text-black dark:text-white text-center text-2xl">
                Sign In to WhatsApp Sender
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

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="p-6.5">
                <div class="mb-4.5">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="Enter your email address"
                        class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 placeholder:text-gray-500 dark:placeholder:text-gray-400" />
                </div>

                <div class="mb-6">
                    <label class="mb-3 block text-sm font-medium text-black dark:text-white">
                        Password
                    </label>
                    <input type="password" name="password" required
                        placeholder="Enter your password"
                        class="w-full rounded border-2 border-gray-300 bg-white px-5 py-3 font-normal text-black outline-none transition focus:border-blue-500 active:border-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white dark:focus:border-blue-400 placeholder:text-gray-500 dark:placeholder:text-gray-400" />
                </div>

                <div class="mb-5" x-data="{ checked: false }">
                    <label class="flex cursor-pointer select-none items-center">
                        <div class="relative">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                class="sr-only"
                                @change="checked = !checked" />
                            <div class="box mr-4 flex h-5 w-5 items-center justify-center rounded border border-primary">
                                <span class="text-primary" :class="checked ? 'opacity-100' : 'opacity-0'">
                                    <svg class="fill-current" width="11" height="8" viewBox="0 0 11 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M10.0915 0.951972L10.0867 0.946075L10.0813 0.940568C9.90076 0.753564 9.61034 0.753146 9.42927 0.939309L4.16201 6.22962L1.58507 3.63469C1.40401 3.44841 1.11351 3.44879 0.932892 3.63584C0.755703 3.81933 0.755703 4.10875 0.932892 4.29224L0.932878 4.29225L0.934851 4.29424L3.58046 6.95832C3.73676 7.11955 3.94983 7.2 4.1473 7.2C4.36196 7.2 4.55963 7.11773 4.71406 6.9584L10.0468 1.60234C10.2436 1.4199 10.2421 1.1339 10.0915 0.951972ZM4.2327 6.30081L4.2317 6.2998C4.23206 6.30015 4.23237 6.30049 4.23269 6.30082L4.2327 6.30081Z" fill="" stroke="" stroke-width="0.4"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <p class="text-sm">Remember me</p>
                    </label>
                </div>

                <button type="submit" class="flex w-full justify-center rounded bg-blue-600 p-3 font-medium text-white hover:bg-blue-700 transition">
                    Sign In
                </button>

                <div class="mt-6 text-center">
                    <p class="text-sm">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="text-primary hover:underline">Sign Up</a>
                    </p>
                </div>
            </div>
        </form>
    </div>

    <div class="mt-4 text-center">
        <p class="text-sm text-bodydark">
            Demo credentials: <strong>admin@whatsapp.com</strong> / <strong>password</strong>
        </p>
    </div>
</div>
@endsection
