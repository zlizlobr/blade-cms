@php
    $currentLocale = app()->getLocale();
    $locales = config('i18n.locale_names', ['cs' => 'Čeština', 'en' => 'English']);
@endphp

<div x-data="{ open: false }" class="relative inline-block text-left">
    <!-- Trigger Button -->
    <button @click="open = !open"
            type="button"
            class="inline-flex items-center justify-center w-full rounded-md px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
        <!-- Globe Icon -->
        <svg class="h-5 w-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span class="uppercase">{{ $currentLocale }}</span>
        <!-- Chevron Icon -->
        <svg class="ml-1 h-4 w-4" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 z-50 mt-2 w-40 origin-top-right rounded-md bg-white dark:bg-gray-800 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
         style="display: none;">
        <div class="py-1">
            @foreach($locales as $locale => $name)
                <form method="POST" action="{{ route('locale.change', $locale) }}" class="block">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition duration-150 ease-in-out
                                   {{ $currentLocale === $locale
                                       ? 'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-semibold'
                                       : 'text-gray-700 dark:text-gray-300' }}">
                        {{ $name }}
                        @if($currentLocale === $locale)
                            <svg class="inline-block ml-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </button>
                </form>
            @endforeach
        </div>
    </div>
</div>
