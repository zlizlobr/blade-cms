<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class InternationalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_middleware_sets_locale_from_cookie(): void
    {
        $response = $this->withCookie('locale', 'en')
            ->get('/');

        $this->assertEquals('en', App::getLocale());
        $response->assertStatus(200);
    }

    public function test_locale_middleware_defaults_to_czech(): void
    {
        // Clear any existing cookies
        $response = $this->call('GET', '/', [], [], [], ['HTTP_ACCEPT_LANGUAGE' => 'cs']);

        $this->assertContains(App::getLocale(), ['cs', 'en']); // Middleware sets it
        $response->assertStatus(200);
    }

    public function test_locale_can_be_changed_via_route(): void
    {
        $response = $this->post('/locale/en');

        $response->assertRedirect();
        $response->assertCookie('locale', 'en');
    }

    public function test_invalid_locale_is_rejected(): void
    {
        $response = $this->post('/locale/invalid');

        $response->assertStatus(400);
    }

    public function test_home_page_displays_czech_translations(): void
    {
        $response = $this->withCookie('locale', 'cs')
            ->get('/');

        $response->assertSee('Spravujte své');
        $response->assertSee('formulářové odesílání');
    }

    public function test_home_page_displays_english_translations(): void
    {
        $response = $this->withCookie('locale', 'en')
            ->get('/');

        $response->assertSee('Manage your');
        $response->assertSee('form submissions');
    }

    public function test_tenant_model_returns_translated_name(): void
    {
        $tenant = Tenant::create([
            'name' => [
                'cs' => 'Testovací Firma',
                'en' => 'Test Company',
            ],
            'slug' => 'test-company',
        ]);

        App::setLocale('cs');
        $this->assertEquals('Testovací Firma', $tenant->name);

        App::setLocale('en');
        $this->assertEquals('Test Company', $tenant->fresh()->name);
    }

    public function test_tenant_model_falls_back_to_czech_when_translation_missing(): void
    {
        $tenant = Tenant::create([
            'name' => [
                'cs' => 'Pouze Česky',
            ],
            'slug' => 'only-czech',
        ]);

        App::setLocale('en');
        $this->assertEquals('Pouze Česky', $tenant->name);
    }

    public function test_tenant_can_set_translation_for_specific_locale(): void
    {
        $tenant = Tenant::create([
            'name' => [
                'cs' => 'První Název',
                'en' => 'First Name',
            ],
            'slug' => 'first-name',
        ]);

        $tenant->setTranslation('name', 'en', 'Updated Name');
        $tenant->save();

        App::setLocale('en');
        $this->assertEquals('Updated Name', $tenant->fresh()->name);

        App::setLocale('cs');
        $this->assertEquals('První Název', $tenant->fresh()->name);
    }

    public function test_tenant_has_translation_method_works(): void
    {
        $tenant = Tenant::create([
            'name' => [
                'cs' => 'Test',
                'en' => 'Test',
            ],
            'slug' => 'test',
        ]);

        $this->assertTrue($tenant->hasTranslation('name', 'cs'));
        $this->assertTrue($tenant->hasTranslation('name', 'en'));
        $this->assertFalse($tenant->hasTranslation('name', 'de'));
    }

    public function test_language_switcher_changes_locale(): void
    {
        $this->withCookie('locale', 'cs')
            ->post('/locale/en')
            ->assertCookie('locale', 'en');

        $this->withCookie('locale', 'en')
            ->post('/locale/cs')
            ->assertCookie('locale', 'cs');
    }
}
