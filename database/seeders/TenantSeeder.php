<?php

namespace Database\Seeders;

use App\Domain\Tenant\Enums\PlanType;
use App\Domain\Tenant\Enums\TenantStatus;
use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tenant 1: Acme Corp (Pro plan)
        Tenant::create([
            'name' => 'Acme Corporation',
            'slug' => 'acme-corp',
            'plan' => PlanType::PRO,
            'status' => TenantStatus::ACTIVE,
        ]);

        // Tenant 2: TechStart (Basic plan)
        Tenant::create([
            'name' => 'TechStart Inc',
            'slug' => 'techstart',
            'plan' => PlanType::BASIC,
            'status' => TenantStatus::ACTIVE,
        ]);

        // Tenant 3: LocalBiz (Free plan)
        Tenant::create([
            'name' => 'Local Business',
            'slug' => 'localbiz',
            'plan' => PlanType::FREE,
            'status' => TenantStatus::ACTIVE,
        ]);
    }
}
