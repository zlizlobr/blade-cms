<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Tenant\Models\Tenant;
use App\Domain\User\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get tenants for assignment
        $acme = Tenant::where('slug', 'acme-corp')->first();
        $techstart = Tenant::where('slug', 'techstart')->first();
        $localbiz = Tenant::where('slug', 'localbiz')->first();

        // 1. Admin User (owner of Acme Corp)
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $admin->tenants()->attach($acme->id, ['role' => 'owner']);

        // 2. Subscriber for Acme Corp (member)
        $subscriber1 = User::factory()->subscriber()->create([
            'name' => 'John Doe',
            'email' => 'john@acme.com',
        ]);
        $subscriber1->tenants()->attach($acme->id, ['role' => 'member']);

        // 3. Subscriber for TechStart (owner)
        $subscriber2 = User::factory()->subscriber()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@techstart.com',
        ]);
        $subscriber2->tenants()->attach($techstart->id, ['role' => 'owner']);

        // 4. Subscriber for LocalBiz (owner)
        $subscriber3 = User::factory()->subscriber()->create([
            'name' => 'Bob Johnson',
            'email' => 'bob@localbiz.com',
        ]);
        $subscriber3->tenants()->attach($localbiz->id, ['role' => 'owner']);

        // 5. Additional member for Acme Corp
        $subscriber4 = User::factory()->subscriber()->create([
            'name' => 'Alice Williams',
            'email' => 'alice@acme.com',
        ]);
        $subscriber4->tenants()->attach($acme->id, ['role' => 'member']);
    }
}
