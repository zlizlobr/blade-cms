<div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-6 pb-4">
    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
            <span class="ml-2 text-xl font-semibold text-gray-900 dark:text-white">
                Admin
            </span>
        </a>
    </div>

    <!-- Navigation -->
    <nav class="flex flex-1 flex-col">
        <ul role="list" class="flex flex-1 flex-col gap-y-7">
            @foreach ($sidebarGroups as $groupName => $items)
                <li>
                    @if ($groupName !== 'default')
                        <div class="text-xs font-semibold leading-6 text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">
                            {{ $groupName }}
                        </div>
                    @endif
                    <ul role="list" class="-mx-2 space-y-1">
                        @foreach ($items as $item)
                            @if (!isset($item['can']) || auth()->user()?->can($item['can']))
                                <li>
                                    @php
                                        $isActive = isset($item['active'])
                                            ? request()->routeIs($item['active'])
                                            : request()->routeIs($item['route'] . '*');
                                    @endphp
                                    <a href="{{ route($item['route']) }}"
                                       class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold {{ $isActive ? 'bg-gray-50 dark:bg-gray-900 text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-900' }}">
                                        @if (isset($item['icon']))
                                            <x-admin-icon :name="$item['icon']" class="h-6 w-6 shrink-0" />
                                        @endif
                                        {{ $item['label'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </li>
            @endforeach

            <!-- Settings section -->
            <li class="mt-auto">
                <ul role="list" class="-mx-2 space-y-1">
                    <li>
                        <a href="{{ route('profile.edit') }}"
                           class="group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-gray-50 dark:hover:bg-gray-900">
                            <x-admin-icon name="settings" class="h-6 w-6 shrink-0" />
                            Settings
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>
