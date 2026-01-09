@extends('layouts.admin')

@section('title', 'Submission Detail')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            Submission #{{ $submission->id }}
        </div>
        <a href="{{ route('admin.submissions.index') }}"
           class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
            ← Back to list
        </a>
    </div>
@endsection

@section('content')
    <!-- Submission Info -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Form Data Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        Form Data
                    </h2>
                </div>
                <div class="px-6 py-4">
                    @if($submission->data)
                        <dl class="space-y-4">
                            @foreach($submission->data as $key => $value)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase">
                                        {{ ucfirst(str_replace('_', ' ', $key)) }}
                                    </dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                        @if(is_array($value))
                                            <pre class="mt-2 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg overflow-x-auto"><code>{{ json_encode($value, JSON_PRETTY_PRINT) }}</code></pre>
                                        @else
                                            <div class="p-3 bg-gray-50 dark:bg-gray-900 rounded-lg">
                                                {{ $value }}
                                            </div>
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            No form data available.
                        </p>
                    @endif
                </div>
            </div>

            <!-- Raw JSON Data -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        Raw JSON Data
                    </h2>
                </div>
                <div class="px-6 py-4">
                    <pre class="p-4 bg-gray-50 dark:bg-gray-900 rounded-lg overflow-x-auto text-sm"><code>{{ json_encode($submission->data, JSON_PRETTY_PRINT) }}</code></pre>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Meta Info Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        Information
                    </h2>
                </div>
                <div class="px-6 py-4 space-y-4">
                    <!-- Form Type -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Form Type
                        </dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                {{ $submission->form_type }}
                            </span>
                        </dd>
                    </div>

                    <!-- Submitted By -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Submitted By
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            @if($submission->user)
                                <div>{{ $submission->user->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $submission->user->email }}
                                </div>
                            @else
                                <span class="text-gray-500 dark:text-gray-400">Guest</span>
                            @endif
                        </dd>
                    </div>

                    <!-- Submitted At -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Submitted At
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            <div>{{ $submission->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $submission->created_at->format('h:i A') }} ({{ $submission->created_at->diffForHumans() }})
                            </div>
                        </dd>
                    </div>

                    <!-- Submission ID -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            Submission ID
                        </dt>
                        <dd class="mt-1 text-sm font-mono text-gray-900 dark:text-white">
                            #{{ $submission->id }}
                        </dd>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white">
                        Actions
                    </h2>
                </div>
                <div class="px-6 py-4 space-y-3">
                    <button type="button"
                            onclick="navigator.clipboard.writeText(document.getElementById('raw-json').textContent)"
                            class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Copy JSON
                    </button>
                    <a href="{{ route('admin.submissions.index') }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden element for copy functionality -->
    <div id="raw-json" class="hidden">{{ json_encode($submission->data, JSON_PRETTY_PRINT) }}</div>
@endsection
