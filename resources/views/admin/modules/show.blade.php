@extends('admin::layouts.admin')

@section('title', $module->name)

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.modules.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 mb-2 inline-block">
                &larr; Back to Modules
            </a>
            <h1 class="text-3xl font-bold leading-tight tracking-tight text-gray-900 dark:text-white">
                {{ $module->name }}
            </h1>
        </div>

        <!-- Status Badge -->
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
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
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Description -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Description</h2>
                <p class="text-gray-700 dark:text-gray-300">
                    {{ $module->description ?? 'No description available.' }}
                </p>
            </div>

            <!-- Dependencies -->
            @if($dependencies && count($dependencies) > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Dependencies</h2>
                    <div class="space-y-3">
                        @foreach($dependencies as $depSlug => $constraint)
                            @php
                                $depModule = app(\App\Domain\Module\Repositories\ModuleRepositoryInterface::class)->findBySlug($depSlug);
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-900 rounded-md">
                                <div class="flex items-center space-x-3">
                                    @if($depModule)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                            @if($depModule->status->value === 'active')
                                                bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                                            @else
                                                bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200
                                            @endif
                                        ">
                                            @if($depModule->status->value === 'active')
                                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                            {{ $depModule->status->value }}
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $depModule->name }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                v{{ $depModule->version }} (requires {{ $constraint }})
                                            </p>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                            not installed
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $depSlug }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                requires {{ $constraint }}
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                @if($depModule)
                                    <a href="{{ route('admin.modules.show', $depSlug) }}"
                                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                                        View
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Module Info -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Module Information</h2>
                <dl class="space-y-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Slug</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white font-mono">{{ $module->slug }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Version</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $module->version }}</dd>
                    </div>
                    @if($module->core_compatibility)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Core Compatibility</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $module->core_compatibility }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Installed</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($module->installed_at)
                                {{ $module->installed_at->format('M d, Y') }}
                                <span class="text-gray-500 dark:text-gray-400">({{ $module->installed_at->diffForHumans() }})</span>
                            @else
                                N/A
                            @endif
                        </dd>
                    </div>
                    @if($module->enabled_at)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Activated</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                {{ $module->enabled_at->format('M d, Y') }}
                                <span class="text-gray-500 dark:text-gray-400">({{ $module->enabled_at->diffForHumans() }})</span>
                            </dd>
                        </div>
                    @endif
                    @if($module->tenant_id)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Scope</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">Tenant-specific</dd>
                        </div>
                    @else
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Scope</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">Global</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Actions</h2>
                <div class="space-y-3">
                    @if($module->status->value === 'active')
                        @if($canDeactivate)
                            <form action="{{ route('admin.modules.deactivate', $module->slug) }}" method="POST">
                                @csrf
                                @method('POST')
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to deactivate this module?')"
                                        class="w-full px-4 py-2 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-md">
                                    Deactivate Module
                                </button>
                            </form>
                        @else
                            <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-md">
                                <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                    Cannot deactivate: other modules depend on this module.
                                </p>
                            </div>
                        @endif
                    @else
                        @if($canActivate)
                            <form action="{{ route('admin.modules.activate', $module->slug) }}" method="POST">
                                @csrf
                                @method('POST')
                                <button type="submit"
                                        class="w-full px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">
                                    Activate Module
                                </button>
                            </form>
                        @else
                            <div class="p-3 bg-red-50 dark:bg-red-900/20 rounded-md">
                                <p class="text-sm text-red-800 dark:text-red-200">
                                    Cannot activate: dependencies not satisfied or version mismatch.
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
