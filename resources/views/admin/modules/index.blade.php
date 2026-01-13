@extends('admin::layouts.admin')

@section('title', 'Modules')

@section('header', 'Modules')

@section('content')
    <!-- Module Status Filter -->
    <div class="mb-6 flex gap-2">
        <select name="status"
                onchange="window.location.href='{{ route('admin.modules.index') }}?status=' + this.value"
                class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">All Modules</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="installed" {{ request('status') === 'installed' ? 'selected' : '' }}>Installed</option>
        </select>
    </div>

    <!-- Modules Grid -->
    @if($modules->count() > 0)
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($modules as $module)
                <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                    <div class="p-6">
                        <!-- Module Header -->
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $module->name }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    {{ $module->slug }}
                                </p>
                            </div>

                            <!-- Status Badge -->
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($module->status->value === 'active')
                                    bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                @elseif($module->status->value === 'inactive')
                                    bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300
                                @else
                                    bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                                @endif
                            ">
                                {{ ucfirst($module->status->value) }}
                            </span>
                        </div>

                        <!-- Description -->
                        @if($module->description)
                            <p class="mt-4 text-sm text-gray-600 dark:text-gray-300 line-clamp-2">
                                {{ $module->description }}
                            </p>
                        @endif

                        <!-- Module Info -->
                        <div class="mt-4 space-y-2">
                            <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                Version {{ $module->version }}
                            </div>

                            @if($module->core_compatibility)
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Core {{ $module->core_compatibility }}
                                </div>
                            @endif

                            @if($module->getDependencies() && count($module->getDependencies()) > 0)
                                <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                    {{ count($module->getDependencies()) }} dependencies
                                </div>
                            @endif
                        </div>

                        <!-- Activation Date -->
                        @if($module->enabled_at)
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Activated {{ $module->enabled_at->diffForHumans() }}
                                </p>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="mt-6 flex gap-2">
                            <a href="{{ route('admin.modules.show', $module->slug) }}"
                               class="flex-1 text-center px-4 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 border border-indigo-600 dark:border-indigo-400 rounded-md hover:bg-indigo-50 dark:hover:bg-gray-700">
                                Details
                            </a>

                            @if($module->status->value === 'active')
                                <form action="{{ route('admin.modules.deactivate', $module->slug) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                            onclick="return confirm('Are you sure you want to deactivate this module?')"
                                            class="w-full px-4 py-2 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-md">
                                        Deactivate
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.modules.activate', $module->slug) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('POST')
                                    <button type="submit"
                                            class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">
                                        Activate
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-lg bg-white dark:bg-gray-800 shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No modules found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Get started by installing your first module.
            </p>
        </div>
    @endif
@endsection
