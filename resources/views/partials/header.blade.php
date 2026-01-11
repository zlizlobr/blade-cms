<header class="bg-white dark:bg-gray-800 shadow">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center">
                    <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    <span class="ml-2 text-xl font-semibold text-gray-900 dark:text-white">
                        {{ config('app.name', 'BladeCMS') }}
                    </span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden sm:flex sm:items-center sm:space-x-8">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 transition duration-150 ease-in-out
                          {{ request()->routeIs('home')
                              ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100'
                              : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}">
                    {{ __('app.nav.home') }}
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 transition duration-150 ease-in-out">
                        {{ __('app.nav.dashboard') }}
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700 transition duration-150 ease-in-out">
                            {{ __('app.nav.admin') }}
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Right Side (Auth Links) -->
            <div class="hidden sm:flex sm:items-center sm:space-x-4">
                <!-- Language Switcher -->
                <x-language-switcher />

                @auth
                    <span class="text-sm text-gray-700 dark:text-gray-300">
                        {{ auth()->user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            {{ __('app.nav.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 transition duration-150 ease-in-out">
                        {{ __('app.nav.login') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                        {{ __('app.nav.register') }}
                    </a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="flex items-center sm:hidden">
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                        type="button"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileMenuOpen, 'inline-flex': !mobileMenuOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileMenuOpen, 'inline-flex': mobileMenuOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div x-data="{ mobileMenuOpen: false }" x-show="mobileMenuOpen" class="sm:hidden">
            <div class="pt-2 pb-3 space-y-1">
                <!-- Language Switcher Mobile -->
                <div class="px-3 pb-2">
                    <x-language-switcher />
                </div>

                <a href="{{ route('home') }}"
                   class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium transition duration-150 ease-in-out
                          {{ request()->routeIs('home')
                              ? 'border-indigo-400 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50'
                              : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    {{ __('app.nav.home') }}
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition duration-150 ease-in-out">
                        {{ __('app.nav.dashboard') }}
                    </a>

                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition duration-150 ease-in-out">
                            {{ __('app.nav.admin') }}
                        </a>
                    @endif

                    <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4">
                            <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ auth()->user()->name }}</div>
                            <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="block w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition duration-150 ease-in-out">
                                    {{ __('app.nav.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                       class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition duration-150 ease-in-out">
                        {{ __('app.nav.login') }}
                    </a>
                    <a href="{{ route('register') }}"
                       class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-300 dark:hover:border-gray-600 transition duration-150 ease-in-out">
                        {{ __('app.nav.register') }}
                    </a>
                @endauth
            </div>
        </div>
    </nav>
</header>
