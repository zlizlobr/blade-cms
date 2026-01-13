<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Domain\FormSubmission\Events\FormSubmitted;
use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    use RefreshDatabase;

    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a tenant for testing
        $this->tenant = Tenant::create([
            'name' => [
                'cs' => 'Testovací Tenant',
                'en' => 'Test Tenant',
            ],
            'slug' => 'test-tenant',
        ]);

        // Set tenant context
        app()->instance('tenant.id', $this->tenant->id);
    }

    /** @test */
    public function user_can_submit_contact_form(): void
    {
        $response = $this->post(route('forms.submit'), [
            'form_type' => 'contact',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'This is a test message',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('form_submissions', [
            'form_type' => 'contact',
            'tenant_id' => $this->tenant->id,
        ]);
    }

    /** @test */
    public function validation_requires_name_email_and_message(): void
    {
        $response = $this->post(route('forms.submit'), [
            'form_type' => 'contact',
        ]);

        $response->assertSessionHasErrors(['name', 'email', 'message']);
    }

    /** @test */
    public function validation_requires_valid_email(): void
    {
        $response = $this->post(route('forms.submit'), [
            'form_type' => 'contact',
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'message' => 'Test message',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function form_submission_creates_record_in_database(): void
    {
        $this->post(route('forms.submit'), [
            'form_type' => 'contact',
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'message' => 'Hello World',
        ]);

        $submission = FormSubmission::first();

        $this->assertNotNull($submission);
        $this->assertEquals('contact', $submission->form_type);
        $this->assertEquals($this->tenant->id, $submission->tenant_id);
        $this->assertEquals('Jane Smith', $submission->data['name']);
        $this->assertEquals('jane@example.com', $submission->data['email']);
        $this->assertEquals('Hello World', $submission->data['message']);
    }

    /** @test */
    public function form_submission_finds_or_creates_user(): void
    {
        // First submission - should create user
        $this->post(route('forms.submit'), [
            'form_type' => 'contact',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'First message',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
        ]);

        $userCount = User::where('email', 'john@example.com')->count();
        $this->assertEquals(1, $userCount);

        // Second submission with same email - should reuse user
        $this->post(route('forms.submit'), [
            'form_type' => 'contact',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Second message',
        ]);

        $userCount = User::where('email', 'john@example.com')->count();
        $this->assertEquals(1, $userCount, 'Should not create duplicate user');
    }

    /** @test */
    public function form_submission_via_ajax_returns_json(): void
    {
        $response = $this->postJson(route('forms.submit'), [
            'form_type' => 'contact',
            'name' => 'AJAX User',
            'email' => 'ajax@example.com',
            'message' => 'AJAX message',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'success' => true,
            'message' => 'Thank you for your submission! We will get back to you soon.',
        ]);
        $response->assertJsonStructure([
            'success',
            'message',
            'submission' => ['id', 'created_at'],
        ]);
    }

    /** @test */
    public function form_submission_event_is_dispatched(): void
    {
        Event::fake([FormSubmitted::class]);

        $response = $this->post(route('forms.submit'), [
            'form_type' => 'contact',
            'name' => 'Event Test',
            'email' => 'event@example.com',
            'message' => 'Testing event',
        ]);

        $response->assertRedirect(); // Non-AJAX requests redirect back

        Event::assertDispatched(FormSubmitted::class, function ($event) {
            return $event->submission->form_type === 'contact'
                && isset($event->submission->data['email'])
                && $event->submission->data['email'] === 'event@example.com';
        });
    }
}
