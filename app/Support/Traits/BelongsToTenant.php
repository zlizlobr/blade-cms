<?php

namespace App\Support\Traits;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /**
     * Boot the BelongsToTenant trait for a model.
     */
    protected static function bootBelongsToTenant(): void
    {
        // Automatically scope queries to current tenant
        static::addGlobalScope('tenant', function (Builder $query) {
            if ($tenantId = app('tenant.id')) {
                $query->where('tenant_id', $tenantId);
            }
        });

        // Automatically set tenant_id when creating new records
        static::creating(function ($model) {
            if (!$model->tenant_id && $tenantId = app('tenant.id')) {
                $model->tenant_id = $tenantId;
            }
        });
    }

    /**
     * Get the tenant that owns this model.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
