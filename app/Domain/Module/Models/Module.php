<?php

declare(strict_types=1);

namespace App\Domain\Module\Models;

use App\Domain\Module\Enums\ModuleStatus;
use App\Domain\Tenant\Models\Tenant;
use App\Support\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Module extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'status',
        'core_compatibility',
        'dependencies',
        'enabled_at',
        'installed_at',
        'tenant_id',
    ];

    protected function casts(): array
    {
        return [
            'status' => ModuleStatus::class,
            'dependencies' => 'array',
            'enabled_at' => 'datetime',
            'installed_at' => 'datetime',
        ];
    }

    /**
     * Override BelongsToTenant trait to support nullable tenant_id for global modules.
     */
    protected static function bootBelongsToTenant(): void
    {
        // Automatically scope queries to current tenant + global modules (tenant_id = NULL)
        static::addGlobalScope('tenant', function (Builder $query) {
            if (app()->bound('tenant.id') && $tenantId = app('tenant.id')) {
                $query->where(function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)
                        ->orWhereNull('tenant_id'); // Include global modules
                });
            }
        });

        // Auto-set tenant_id when creating, but only if explicitly not null
        static::creating(function ($model) {
            // If tenant_id is explicitly set to null, keep it as global module
            if ($model->tenant_id === null && ! $model->isDirty('tenant_id')) {
                // Auto-assign current tenant if available
                if (app()->bound('tenant.id') && $tenantId = app('tenant.id')) {
                    $model->tenant_id = $tenantId;
                }
            }
        });
    }

    /**
     * Get the tenant that owns this module.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if module is active.
     */
    public function isActive(): bool
    {
        return $this->status === ModuleStatus::ACTIVE;
    }

    /**
     * Check if module is installed.
     */
    public function isInstalled(): bool
    {
        return $this->status === ModuleStatus::INSTALLED;
    }

    /**
     * Check if module is inactive.
     */
    public function isInactive(): bool
    {
        return $this->status === ModuleStatus::INACTIVE;
    }

    /**
     * Get module dependencies.
     */
    public function getDependencies(): array
    {
        return $this->dependencies ?? [];
    }

    /**
     * Scope: Get only active modules.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ModuleStatus::ACTIVE);
    }

    /**
     * Scope: Get only inactive modules.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', ModuleStatus::INACTIVE);
    }

    /**
     * Scope: Get only installed modules.
     */
    public function scopeInstalled(Builder $query): Builder
    {
        return $query->where('status', ModuleStatus::INSTALLED);
    }
}
