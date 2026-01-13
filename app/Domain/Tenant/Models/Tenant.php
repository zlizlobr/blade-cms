<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Models;

use App\Domain\Tenant\Enums\PlanType;
use App\Domain\Tenant\Enums\TenantStatus;
use App\Domain\User\Models\User;
use App\Support\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tenant extends Model
{
    use HasFactory, HasTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'plan',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name' => 'array',
            'plan' => PlanType::class,
            'status' => TenantStatus::class,
        ];
    }

    /**
     * Get the list of translatable attributes.
     */
    protected function getTranslatableAttributes(): array
    {
        return ['name'];
    }

    /**
     * Get the users that belong to this tenant.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'tenant_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return \Database\Factories\TenantFactory::new();
    }
}
