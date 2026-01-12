@extends('theme::layouts.marketing')

@section('title', __('app.hero.title_1') . ' ' . __('app.hero.title_2') . ' - ' . config('app.name'))

@section('content')
    <!-- Hero Section -->
    <section class="relative bg-white dark:bg-gray-900 overflow-hidden">
        <div class="max-w-7xl mx-auto">
            <div class="relative z-10 pb-8 bg-white dark:bg-gray-900 sm:pb-16 md:pb-20 lg:max-w-2xl lg:w-full lg:pb-28 xl:pb-32">
                <main class="mt-10 mx-auto max-w-7xl px-4 sm:mt-12 sm:px-6 md:mt-16 lg:mt-20 lg:px-8 xl:mt-28">
                    <div class="sm:text-center lg:text-left">
                        <h1 class="text-4xl tracking-tight font-extrabold text-gray-900 dark:text-white sm:text-5xl md:text-6xl">
                            <span class="block xl:inline">{{ __('app.hero.title_1') }}</span>
                            <span class="block text-indigo-600 dark:text-indigo-400 xl:inline">{{ __('app.hero.title_2') }}</span>
                        </h1>
                        <p class="mt-3 text-base text-gray-500 dark:text-gray-400 sm:mt-5 sm:text-lg sm:max-w-xl sm:mx-auto md:mt-5 md:text-xl lg:mx-0">
                            {{ __('app.hero.description') }}
                        </p>
                        <div class="mt-5 sm:mt-8 sm:flex sm:justify-center lg:justify-start">
                            @auth
                                <div class="rounded-md shadow">
                                    <a href="{{ route('dashboard') }}"
                                       class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out">
                                        {{ __('app.hero.cta_dashboard') }}
                                    </a>
                                </div>
                            @else
                                <div class="rounded-md shadow">
                                    <a href="{{ route('register') }}"
                                       class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out">
                                        {{ __('app.hero.cta_get_started') }}
                                    </a>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-3">
                                    <a href="#contact"
                                       class="w-full flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-700 dark:text-indigo-300 bg-indigo-100 dark:bg-indigo-900/30 hover:bg-indigo-200 dark:hover:bg-indigo-900/50 md:py-4 md:text-lg md:px-10 transition duration-150 ease-in-out">
                                        {{ __('app.hero.cta_contact') }}
                                    </a>
                                </div>
                            @endauth
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <div class="lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
            <div class="h-56 w-full bg-gradient-to-br from-indigo-500 to-purple-600 dark:from-indigo-600 dark:to-purple-700 sm:h-72 md:h-96 lg:w-full lg:h-full flex items-center justify-center">
                <svg class="h-48 w-48 text-white opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-12 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                    {{ __('app.features.title') }}
                </h2>
                <p class="mt-4 max-w-2xl text-xl text-gray-500 dark:text-gray-400 mx-auto">
                    {{ __('app.features.subtitle') }}
                </p>
            </div>

            <div class="mt-10">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <!-- Feature 1 -->
                    <div class="pt-6">
                        <div class="flow-root bg-white dark:bg-gray-900 rounded-lg px-6 pb-8">
                            <div class="-mt-6">
                                <div class="inline-flex items-center justify-center p-3 bg-indigo-500 dark:bg-indigo-600 rounded-md shadow-lg">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">{{ __('app.features.multi_tenant.title') }}</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    {{ __('app.features.multi_tenant.description') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 2 -->
                    <div class="pt-6">
                        <div class="flow-root bg-white dark:bg-gray-900 rounded-lg px-6 pb-8">
                            <div class="-mt-6">
                                <div class="inline-flex items-center justify-center p-3 bg-indigo-500 dark:bg-indigo-600 rounded-md shadow-lg">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">{{ __('app.features.realtime.title') }}</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    {{ __('app.features.realtime.description') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Feature 3 -->
                    <div class="pt-6">
                        <div class="flow-root bg-white dark:bg-gray-900 rounded-lg px-6 pb-8">
                            <div class="-mt-6">
                                <div class="inline-flex items-center justify-center p-3 bg-indigo-500 dark:bg-indigo-600 rounded-md shadow-lg">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <h3 class="mt-8 text-lg font-medium text-gray-900 dark:text-white tracking-tight">{{ __('app.features.analytics.title') }}</h3>
                                <p class="mt-5 text-base text-gray-500 dark:text-gray-400">
                                    {{ __('app.features.analytics.description') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section id="contact" class="py-16 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl mx-auto">
                <div class="text-center">
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white sm:text-4xl">
                        {{ __('app.contact.title') }}
                    </h2>
                    <p class="mt-4 text-lg text-gray-500 dark:text-gray-400">
                        {{ __('app.contact.subtitle') }}
                    </p>
                </div>

                <!-- Success Message (for AJAX) -->
                <div id="success-message" class="mt-8 rounded-md bg-green-50 dark:bg-green-900/20 p-4 {{ session('success') ? '' : 'hidden' }}">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="message-text text-sm font-medium text-green-800 dark:text-green-200">
                                {{ session('success') ?? __('app.contact.success_message') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Error Message (for AJAX) -->
                <div id="error-message" class="mt-8 rounded-md bg-red-50 dark:bg-red-900/20 p-4 hidden">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="message-text text-sm font-medium text-red-800 dark:text-red-200">
                                {{ __('app.contact.error_message') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <form id="contact-form" action="{{ route('forms.submit') }}" method="POST" class="mt-8 space-y-6">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('app.contact.form.name_label') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name') }}"
                                   required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('name') border-red-300 dark:border-red-600 @enderror"
                                   placeholder="{{ __('app.contact.form.name_placeholder') }}">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('app.contact.form.email_label') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <input type="email"
                                   name="email"
                                   id="email"
                                   value="{{ old('email') }}"
                                   required
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('email') border-red-300 dark:border-red-600 @enderror"
                                   placeholder="{{ __('app.contact.form.email_placeholder') }}">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ __('app.contact.form.message_label') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1">
                            <textarea name="message"
                                      id="message"
                                      rows="5"
                                      required
                                      class="appearance-none block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm placeholder-gray-400 dark:placeholder-gray-500 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm @error('message') border-red-300 dark:border-red-600 @enderror"
                                      placeholder="{{ __('app.contact.form.message_placeholder') }}">{{ old('message') }}</textarea>
                        </div>
                        @error('message')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit"
                                class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            {{ __('app.contact.form.submit_button') }}
                        </button>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        {{ __('app.contact.form.privacy_notice') }}
                    </p>
                </form>
            </div>
        </div>
    </section>
@endsection
