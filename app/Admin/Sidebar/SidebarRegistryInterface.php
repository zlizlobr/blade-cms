<?php

declare(strict_types=1);

namespace App\Admin\Sidebar;

interface SidebarRegistryInterface
{
    /**
     * Add a sidebar item.
     *
     * @param array<string, mixed> $item
     */
    public function add(array $item): void;

    /**
     * Get all sidebar items sorted by order.
     *
     * @return array<int, array<string, mixed>>
     */
    public function all(): array;

    /**
     * Get sidebar items grouped by 'group' key.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function grouped(): array;

    /**
     * Clear internal cache (if any).
     */
    public function clearCache(): void;
}
