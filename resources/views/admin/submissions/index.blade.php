@extends('layouts.admin')

@section('title', 'Form Submissions')

@section('header', 'Form Submissions')

@section('content')
    <!-- Filters and Search -->
    <div class="mb-6 flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
            <form method="GET" action="{{ route('admin.submissions.index') }}" class="flex gap-2">
                <input type="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by name, email..."
                       class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md shadow-sm">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.submissions.index') }}"
                       class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md shadow-sm">
                        Clear
                    </a>
                @endif
            </form>
        </div>
        <div class="flex gap-2">
            <select name="form_type"
                    onchange="window.location.href='{{ route('admin.submissions.index') }}?form_type=' + this.value"
                    class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Types</option>
                <option value="contact" {{ request('form_type') === 'contact' ? 'selected' : '' }}>Contact</option>
            </select>
        </div>
    </div>

    <!-- Submissions Table -->
    @if($submissions->count() > 0)
        <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Form Type
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Submitted By
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Date
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($submissions as $submission)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-500 dark:text-gray-400">
                                    #{{ $submission->id }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200">
                                    {{ $submission->form_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    @if($submission->user)
                                        {{ $submission->user->name }}
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400">Guest</span>
                                    @endif
                                </div>
                                @if($submission->data['email'] ?? null)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $submission->data['email'] }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    {{ $submission->created_at->diffForHumans() }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $submission->created_at->format('M d, Y h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.submissions.show', $submission) }}"
                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $submissions->links() }}
        </div>
    @else
        <div class="rounded-lg bg-white dark:bg-gray-800 shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No submissions</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if(request('search'))
                    No submissions found matching your search criteria.
                @else
                    No form submissions have been received yet.
                @endif
            </p>
            @if(request('search'))
                <div class="mt-6">
                    <a href="{{ route('admin.submissions.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        View all submissions
                    </a>
                </div>
            @endif
        </div>
    @endif
@endsection
