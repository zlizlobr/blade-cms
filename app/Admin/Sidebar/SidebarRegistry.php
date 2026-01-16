<?php

declare(strict_types=1);

namespace App\Admin\Sidebar;

class SidebarRegistry implements SidebarRegistryInterface
{
    /** @var array<int, array<string, mixed>> */
    protected array $items = [];

    public function add(array $item): void
    {
        $this->items[] = $item;
    }

    public function all(): array
    {
        return collect($this->items)
            ->sortBy('order')
            ->values()
            ->all();
    }

    /**
     * Create service instance.
     * Factory method for convenient instantiation outside Laravel container.
     */
    public static function create(): self
    {
        return new self();
    }
}
