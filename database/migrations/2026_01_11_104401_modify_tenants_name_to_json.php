<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, migrate existing data
        $tenants = DB::table('tenants')->get();

        foreach ($tenants as $tenant) {
            $translations = json_encode([
                'cs' => $tenant->name,
                'en' => $tenant->name,
            ]);

            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['name' => $translations]);
        }

        // Then change column type to JSON
        Schema::table('tenants', function (Blueprint $table) {
            $table->json('name')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Extract Czech translation as default
        $tenants = DB::table('tenants')->get();

        foreach ($tenants as $tenant) {
            $translations = json_decode($tenant->name, true);
            $name = $translations['cs'] ?? $translations['en'] ?? 'Unknown';

            DB::table('tenants')
                ->where('id', $tenant->id)
                ->update(['name' => $name]);
        }

        // Change column back to string
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->change();
        });
    }
};
