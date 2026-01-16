<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Module\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ModuleSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'module:sync
                            {--dry-run : Show what would be done without making changes}
                            {--force : Force update even if versions match}
                            {--prune : Remove orphan modules from database that are not in filesystem}';

    /**
     * The console command description.
     */
    protected $description = 'Synchronize modules from filesystem (vendor/bladecms/) to database';

    private int $newCount = 0;

    private int $updatedCount = 0;

    private int $syncedCount = 0;

    private int $orphanCount = 0;

    private int $prunedCount = 0;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning modules...');
        $this->newLine();

        $isDryRun = $this->option('dry-run');
        $isForce = $this->option('force');
        $isPrune = $this->option('prune');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - no changes will be made');
            $this->newLine();
        }

        // Step 1: Scan filesystem for modules
        $filesystemModules = $this->scanFilesystemModules();

        // Step 2: Get all modules from database (without tenant scope)
        $dbModules = Module::withoutGlobalScopes()->get()->keyBy('slug');

        // Step 3: Process each filesystem module
        foreach ($filesystemModules as $slug => $moduleData) {
            $this->processModule($slug, $moduleData, $dbModules, $isDryRun, $isForce);
        }

        // Step 4: Check for orphan records (in DB but not in filesystem)
        $this->processOrphanRecords($dbModules, $filesystemModules, $isDryRun, $isPrune);

        // Step 5: Summary
        $this->newLine();
        $summaryParts = [
            sprintf('%d new', $this->newCount),
            sprintf('%d updated', $this->updatedCount),
            sprintf('%d synced', $this->syncedCount),
        ];

        if ($isPrune) {
            $summaryParts[] = sprintf('%d pruned', $this->prunedCount);
        } else {
            $summaryParts[] = sprintf('%d orphans', $this->orphanCount);
        }

        $this->info('Summary: ' . implode(', ', $summaryParts));

        if ($this->orphanCount > 0 && ! $isPrune) {
            $this->newLine();
            $this->warn('Tip: Use --prune to remove orphan modules from database');
        }

        return Command::SUCCESS;
    }

    /**
     * Scan vendor/bladecms/ directory for modules with module.json.
     *
     * @return array<string, array<string, mixed>>
     */
    private function scanFilesystemModules(): array
    {
        $modules = [];
        $vendorPath = base_path('vendor/bladecms');

        if (! File::isDirectory($vendorPath)) {
            return $modules;
        }

        $directories = File::directories($vendorPath);

        foreach ($directories as $directory) {
            $moduleJsonPath = $directory . '/module.json';

            if (! File::exists($moduleJsonPath)) {
                continue;
            }

            $content = File::get($moduleJsonPath);
            $data = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->warn("Invalid JSON in {$moduleJsonPath}");

                continue;
            }

            if (! isset($data['slug'])) {
                $this->warn("Missing 'slug' in {$moduleJsonPath}");

                continue;
            }

            $modules[$data['slug']] = [
                'name' => $data['name'] ?? $data['slug'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'version' => $data['version'] ?? '1.0.0',
                'core_compatibility' => $data['core_compatibility'] ?? null,
                'dependencies' => $data['dependencies'] ?? [],
                'path' => $directory,
            ];
        }

        return $modules;
    }

    /**
     * Process a single module - insert or update.
     *
     * @param array<string, mixed> $moduleData
     * @param \Illuminate\Support\Collection<string, Module> $dbModules
     */
    private function processModule(
        string $slug,
        array $moduleData,
        $dbModules,
        bool $isDryRun,
        bool $isForce
    ): void {
        $existingModule = $dbModules->get($slug);

        if (! $existingModule) {
            // New module - insert
            $this->line(sprintf(
                '<fg=green>[NEW]</> %s - %s v%s',
                $slug,
                $moduleData['name'],
                $moduleData['version']
            ));

            if (! $isDryRun) {
                Module::create([
                    'name' => $moduleData['name'],
                    'slug' => $moduleData['slug'],
                    'description' => $moduleData['description'],
                    'version' => $moduleData['version'],
                    'status' => ModuleStatus::INSTALLED,
                    'core_compatibility' => $moduleData['core_compatibility'],
                    'dependencies' => $moduleData['dependencies'],
                    'installed_at' => now(),
                    'tenant_id' => null, // Global module
                ]);
            }

            $this->newCount++;

            return;
        }

        // Module exists - check if update needed
        $needsUpdate = $isForce
            || $existingModule->version !== $moduleData['version']
            || $existingModule->name !== $moduleData['name']
            || $existingModule->description !== $moduleData['description']
            || $existingModule->core_compatibility !== $moduleData['core_compatibility'];

        if ($needsUpdate) {
            $versionInfo = $existingModule->version !== $moduleData['version']
                ? " (v{$existingModule->version} → v{$moduleData['version']})"
                : '';

            $this->line(sprintf(
                '<fg=yellow>[UPDATE]</> %s - %s%s',
                $slug,
                $moduleData['name'],
                $versionInfo
            ));

            if (! $isDryRun) {
                $existingModule->update([
                    'name' => $moduleData['name'],
                    'description' => $moduleData['description'],
                    'version' => $moduleData['version'],
                    'core_compatibility' => $moduleData['core_compatibility'],
                    'dependencies' => $moduleData['dependencies'],
                ]);
            }

            $this->updatedCount++;

            return;
        }

        // Already in sync
        $this->line(sprintf(
            '<fg=blue>[OK]</> %s - already in sync',
            $slug
        ));

        $this->syncedCount++;
    }

    /**
     * Process orphan modules - either warn or delete based on prune option.
     *
     * @param \Illuminate\Support\Collection<string, Module> $dbModules
     * @param array<string, array<string, mixed>> $filesystemModules
     */
    private function processOrphanRecords(
        $dbModules,
        array $filesystemModules,
        bool $isDryRun,
        bool $isPrune
    ): void {
        foreach ($dbModules as $slug => $module) {
            if (isset($filesystemModules[$slug])) {
                continue;
            }

            if ($isPrune) {
                $this->line(sprintf(
                    '<fg=red>[PRUNE]</> %s - removed from database',
                    $slug
                ));

                if (! $isDryRun) {
                    $module->delete();
                }

                $this->prunedCount++;
            } else {
                $this->line(sprintf(
                    '<fg=red>[ORPHAN]</> %s - in DB but not found in filesystem',
                    $slug
                ));

                $this->orphanCount++;
            }
        }
    }
}
