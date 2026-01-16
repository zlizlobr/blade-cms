<?php

declare(strict_types=1);

namespace App\Admin\Sidebar;

class SidebarRegistry implements SidebarRegistryInterface
{
    /** @var array<int, array<string, mixed>> */
    protected array $items = [];

    /** @var array<int, array<string, mixed>>|null */
    protected ?array $cachedAll = null;

    /** @var array<string, array<int, array<string, mixed>>>|null */
    protected ?array $cachedGrouped = null;

    public function add(array $item): void
    {
        $this->items[] = $item;
        $this->clearCache();
    }

    public function all(): array
    {
        if ($this->cachedAll === null) {
            $this->cachedAll = collect($this->items)
                ->sortBy('order')
                ->values()
                ->all();
        }

        return $this->cachedAll;
    }

    public function grouped(): array
    {
        if ($this->cachedGrouped === null) {
            $this->cachedGrouped = collect($this->items)
                ->sortBy('order')
                ->groupBy(fn (array $item): string => $item['group'] ?? 'default')
                ->map(fn ($items) => $items->values()->all())
                ->all();
        }

        return $this->cachedGrouped;
    }

    public function clearCache(): void
    {
        $this->cachedAll = null;
        $this->cachedGrouped = null;
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
