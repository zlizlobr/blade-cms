<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed tenants first (they need to exist before users can be attached)
        $this->call([
            TenantSeeder::class,
            UserSeeder::class,
        ]);
    }
}
