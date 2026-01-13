<?php

declare(strict_types=1);

namespace Tests\Views;

use Tests\TestCase;

class ComponentTest extends TestCase
{
    public function test_primary_button_component_renders(): void
    {
        $view = $this->blade('<x-primary-button>Click me</x-primary-button>');

        $view->assertSee('Click me');
        $view->assertSee('button', false);
    }

    public function test_dropdown_link_component_renders(): void
    {
        $view = $this->blade('<x-dropdown-link href="/test">Link Text</x-dropdown-link>');

        $view->assertSee('Link Text');
        $view->assertSee('/test', false);
    }

    public function test_language_switcher_renders_button(): void
    {
        $view = $this->blade('<x-language-switcher />');

        $view->assertSee('button', false);
        $view->assertSee('x-data', false);
    }
}
