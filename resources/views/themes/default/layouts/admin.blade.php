<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Admin') - {{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <!-- Mobile sidebar backdrop -->
            <div x-show="sidebarOpen"
                 x-transition:enter="transition-opacity ease-linear duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition-opacity ease-linear duration-300"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false"
                 class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
                 style="display: none;"></div>

            <!-- Mobile sidebar -->
            <div x-show="sidebarOpen"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 lg:hidden"
                 style="display: none;">
                @include('theme::partials.admin-sidebar')
            </div>

            <!-- Desktop sidebar -->
            <div class="hidden lg:fixed lg:inset-y-0 lg:flex lg:w-64 lg:flex-col">
                @include('theme::partials.admin-sidebar')
            </div>

            <!-- Main content -->
            <div class="lg:pl-64">
                <!-- Top navigation -->
                <div class="sticky top-0 z-10 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                    <!-- Mobile menu button -->
                    <button @click="sidebarOpen = true"
                            type="button"
                            class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-200 lg:hidden">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <div class="flex flex-1"></div>

                        <!-- User menu -->
                        <div class="flex items-center gap-x-4 lg:gap-x-6">
                            <!-- Current tenant indicator -->
                            @if(auth()->user()->currentTenant())
                                <div class="hidden sm:flex items-center gap-x-2 text-sm text-gray-700 dark:text-gray-300">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span>{{ auth()->user()->currentTenant()->name }}</span>
                                </div>
                            @endif

                            <!-- User dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                        type="button"
                                        class="flex items-center gap-x-2 text-sm font-semibold leading-6 text-gray-900 dark:text-white"
                                        id="user-menu-button">
                                    <span class="sr-only">Open user menu</span>
                                    <span>{{ auth()->user()->name }}</span>
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>

                                <!-- Dropdown menu -->
                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 py-2 shadow-lg ring-1 ring-gray-900/5 dark:ring-gray-700 focus:outline-none"
                                     style="display: none;">
                                    <a href="{{ route('home') }}"
                                       class="block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        View Site
                                    </a>
                                    <a href="{{ route('profile.edit') }}"
                                       class="block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        Your Profile
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                                class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main content area -->
                <main class="py-10">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <!-- Page header -->
                        @hasSection('header')
                            <div class="mb-8">
                                <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white">
                                    @yield('header')
                                </h1>
                            </div>
                        @endif

                        <!-- Flash messages -->
                        @if(session('success'))
                            <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                            {{ session('success') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-6 rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-red-800 dark:text-red-200">
                                            {{ session('error') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Page content -->
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
