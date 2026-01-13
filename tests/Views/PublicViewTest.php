<?php

declare(strict_types=1);

namespace Tests\Views;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicViewTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Create tenant for view context
        $this->tenant = Tenant::create([
            'name' => [
                'cs' => 'Testovací Tenant',
                'en' => 'Test Tenant',
            ],
            'slug' => 'test-tenant',
        ]);

        app()->instance('tenant.id', $this->tenant->id);
    }

    public function test_home_page_renders_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('public::pages.home');
    }

    public function test_language_switcher_component_renders(): void
    {
        $view = $this->blade('<x-language-switcher />');

        $view->assertSee('button', false);
    }

    public function test_public_pages_use_correct_layout(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('<!DOCTYPE html>', false);
    }
}
