<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Admin\Sidebar\SidebarRegistry;
use PHPUnit\Framework\TestCase;

class SidebarRegistryTest extends TestCase
{
    private SidebarRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new SidebarRegistry();
    }

    public function test_can_add_item(): void
    {
        $this->registry->add([
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'order' => 10,
        ]);

        $items = $this->registry->all();

        $this->assertCount(1, $items);
        $this->assertEquals('Dashboard', $items[0]['label']);
    }

    public function test_items_are_sorted_by_order(): void
    {
        $this->registry->add(['label' => 'Third', 'route' => 'third', 'order' => 30]);
        $this->registry->add(['label' => 'First', 'route' => 'first', 'order' => 10]);
        $this->registry->add(['label' => 'Second', 'route' => 'second', 'order' => 20]);

        $items = $this->registry->all();

        $this->assertEquals('First', $items[0]['label']);
        $this->assertEquals('Second', $items[1]['label']);
        $this->assertEquals('Third', $items[2]['label']);
    }

    public function test_items_are_grouped_by_group_key(): void
    {
        $this->registry->add(['label' => 'Dashboard', 'route' => 'dashboard', 'order' => 10]);
        $this->registry->add(['label' => 'Users', 'route' => 'users', 'order' => 20, 'group' => 'Admin']);
        $this->registry->add(['label' => 'Roles', 'route' => 'roles', 'order' => 30, 'group' => 'Admin']);
        $this->registry->add(['label' => 'Posts', 'route' => 'posts', 'order' => 40, 'group' => 'Content']);

        $grouped = $this->registry->grouped();

        $this->assertArrayHasKey('default', $grouped);
        $this->assertArrayHasKey('Admin', $grouped);
        $this->assertArrayHasKey('Content', $grouped);

        $this->assertCount(1, $grouped['default']);
        $this->assertCount(2, $grouped['Admin']);
        $this->assertCount(1, $grouped['Content']);

        $this->assertEquals('Dashboard', $grouped['default'][0]['label']);
        $this->assertEquals('Users', $grouped['Admin'][0]['label']);
        $this->assertEquals('Roles', $grouped['Admin'][1]['label']);
    }

    public function test_cache_is_cleared_when_adding_items(): void
    {
        $this->registry->add(['label' => 'First', 'route' => 'first', 'order' => 10]);

        // Access to populate cache
        $this->registry->all();
        $this->registry->grouped();

        // Add new item - should clear cache
        $this->registry->add(['label' => 'Second', 'route' => 'second', 'order' => 20]);

        $items = $this->registry->all();

        $this->assertCount(2, $items);
    }

    public function test_clear_cache_method(): void
    {
        $this->registry->add(['label' => 'Test', 'route' => 'test', 'order' => 10]);

        // Access to populate cache
        $this->registry->all();

        // Clear cache
        $this->registry->clearCache();

        // Should still return same data
        $items = $this->registry->all();

        $this->assertCount(1, $items);
    }

    public function test_factory_method_creates_instance(): void
    {
        $registry = SidebarRegistry::create();

        $this->assertInstanceOf(SidebarRegistry::class, $registry);
    }

    public function test_items_without_order_are_handled(): void
    {
        $this->registry->add(['label' => 'No Order', 'route' => 'no-order']);
        $this->registry->add(['label' => 'With Order', 'route' => 'with-order', 'order' => 10]);

        $items = $this->registry->all();

        $this->assertCount(2, $items);
    }
}
