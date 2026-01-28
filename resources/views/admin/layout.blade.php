<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - WhatsApp Sender Admin</title>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body x-data="{ page: 'dashboard', 'loaded': true, 'darkMode': false, 'stickyMenu': false, 'sidebarToggle': false, 'scrollTop': false }" x-init="
         darkMode = JSON.parse(localStorage.getItem('darkMode')) ?? false;
         $watch('darkMode', value => localStorage.setItem('darkMode', JSON.stringify(value)))" :class="{'dark text-bodydark bg-boxdark-2': darkMode === true}">
    
    <!-- Page Wrapper -->
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')
        
        <!-- Content Area -->
        <div class="relative flex flex-1 flex-col overflow-y-auto overflow-x-hidden">
            <!-- Header -->
            @include('admin.partials.header')
            
            <!-- Main Content -->
            <main>
                <div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
                    <!-- Breadcrumb -->
                    @yield('breadcrumb')
                    
                    <!-- Alerts -->
                    @if(session('success'))
                    <div class="mb-4 flex w-full border-l-6 border-[#34D399] bg-[#34D399] bg-opacity-[15%] px-7 py-8 shadow-md dark:bg-[#1B1B24] dark:bg-opacity-30 md:p-9">
                        <div class="mr-5 flex h-9 w-9 items-center justify-center rounded-lg bg-[#34D399]">
                            <svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.2984 0.826822L15.2868 0.811827L15.2741 0.797751C14.9173 0.401867 14.3238 0.400754 13.9657 0.794406L5.91888 9.45376L2.05667 5.2868C1.69856 4.89287 1.10487 4.89389 0.747996 5.28987C0.417335 5.65675 0.417335 6.22337 0.747996 6.59026L0.747959 6.59029L0.752701 6.59541L4.86742 11.0348C5.14445 11.3405 5.52858 11.5 5.89581 11.5C6.29242 11.5 6.65178 11.3355 6.92401 11.035L15.2162 2.11161C15.5833 1.74452 15.576 1.18615 15.2984 0.826822Z" fill="white" stroke="white"></path>
                            </svg>
                        </div>
                        <div class="w-full">
                            <h5 class="mb-3 text-lg font-semibold text-black dark:text-[#34D399]">
                                Success
                            </h5>
                            <p class="text-base leading-relaxed text-body">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                    @endif
                    
                    @if(session('error'))
                    <div class="mb-4 flex w-full border-l-6 border-[#F87171] bg-[#F87171] bg-opacity-[15%] px-7 py-8 shadow-md dark:bg-[#1B1B24] dark:bg-opacity-30 md:p-9">
                        <div class="mr-5 flex h-9 w-full max-w-[36px] items-center justify-center rounded-lg bg-[#F87171]">
                            <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.4917 7.65579L11.106 12.2645C11.2545 12.4128 11.4715 12.5 11.6738 12.5C11.8762 12.5 12.0931 12.4128 12.2416 12.2645C12.5621 11.9445 12.5623 11.4317 12.2423 11.1114C12.2422 11.1113 12.2422 11.1113 12.2422 11.1113C12.242 11.1111 12.2418 11.1109 12.2416 11.1107L7.64539 6.50351C8.19716 5.91217 8.19716 4.98169 7.64539 4.39035L12.2589 -0.227951C12.5794 -0.547937 12.5794 -1.06045 12.2589 -1.38044C11.9384 -1.70043 11.4259 -1.70043 11.1054 -1.38044L6.4917 3.23213C5.90053 2.68075 4.97016 2.68075 4.37898 3.23213L-0.220513 -1.38114C-0.540999 -1.70088 -1.05373 -1.70088 -1.37421 -1.38114C-1.69469 -1.06139 -1.69469 -0.548817 -1.37421 -0.229072L3.23779 4.38108C2.68634 4.97285 2.68634 5.90335 3.23779 6.49511L-1.37421 11.1058C-1.69469 11.4256 -1.69469 11.9381 -1.37421 12.2579C-1.05373 12.5776 -0.540999 12.5776 -0.220513 12.2579L4.37898 7.65571C4.97016 8.20709 5.90053 8.20709 6.4917 7.65579Z" fill="white"></path>
                            </svg>
                        </div>
                        <div class="w-full">
                            <h5 class="mb-3 font-semibold text-[#B45454]">
                                Error
                            </h5>
                            <p class="text-base leading-relaxed text-[#CD5D5D]">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
