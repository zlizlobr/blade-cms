<?php

declare(strict_types=1);

namespace Tests\Views;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_view_renders(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
        $response->assertSee('Log in');
    }

    public function test_register_view_renders(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.register');
        $response->assertSee('Register');
    }

    public function test_forgot_password_view_renders(): void
    {
        $response = $this->get(route('password.request'));

        $response->assertStatus(200);
        $response->assertViewIs('auth.forgot-password');
    }

    public function test_reset_password_view_renders(): void
    {
        $response = $this->get(route('password.reset', ['token' => 'fake-token']));

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
    }

    public function test_auth_guest_layout_renders(): void
    {
        $view = $this->view('auth.layouts.guest');

        $view->assertSee('html', false);
    }
}
