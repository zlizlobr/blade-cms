<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\FormSubmission\Models\FormSubmission;
use App\Domain\FormSubmission\Repositories\FormSubmissionRepositoryInterface;
use App\Domain\FormSubmission\Services\FormSubmissionService;
use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormSubmissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private FormSubmissionService $service;
    private Tenant $tenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tenant = Tenant::create([
            'name' => [
                'cs' => 'Test Tenant',
                'en' => 'Test Tenant',
            ],
            'slug' => 'test-tenant',
        ]);

        app()->instance('tenant.id', $this->tenant->id);

        $repository = app(FormSubmissionRepositoryInterface::class);
        $this->service = new FormSubmissionService($repository);
    }

    public function test_can_create_service_instance_with_factory_method(): void
    {
        $service = FormSubmissionService::create();

        $this->assertInstanceOf(FormSubmissionService::class, $service);
    }

    public function test_creates_submission_with_existing_user(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'name' => 'Existing User',
        ]);

        $data = [
            'form_type' => 'contact',
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'message' => 'Test message',
        ];

        $submission = $this->service->createSubmission($data);

        $this->assertInstanceOf(FormSubmission::class, $submission);
        $this->assertEquals($user->id, $submission->user_id);
        $this->assertEquals('contact', $submission->form_type);
        $this->assertEquals('existing@example.com', $submission->data['email']);
    }

    public function test_creates_new_user_when_email_not_exists(): void
    {
        $userCountBefore = User::count();

        $data = [
            'form_type' => 'contact',
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'message' => 'Test message',
        ];

        $submission = $this->service->createSubmission($data);

        $this->assertEquals($userCountBefore + 1, User::count());
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
            'name' => 'New User',
        ]);

        $this->assertEquals('newuser@example.com', $submission->data['email']);
    }

    public function test_creates_submission_with_correct_data_structure(): void
    {
        $data = [
            'form_type' => 'newsletter',
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Subscribe me',
        ];

        $submission = $this->service->createSubmission($data);

        $this->assertEquals('newsletter', $submission->form_type);
        $this->assertIsArray($submission->data);
        $this->assertArrayHasKey('name', $submission->data);
        $this->assertArrayHasKey('email', $submission->data);
        $this->assertArrayHasKey('message', $submission->data);
    }

    public function test_submission_belongs_to_current_tenant(): void
    {
        $data = [
            'form_type' => 'contact',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ];

        $submission = $this->service->createSubmission($data);

        $this->assertEquals($this->tenant->id, $submission->tenant_id);
    }
}
