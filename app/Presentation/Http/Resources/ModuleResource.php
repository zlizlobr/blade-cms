<?php

declare(strict_types=1);

namespace App\Presentation\Http\Resources;

use App\Domain\Module\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Module
 */
class ModuleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'version' => $this->version,
            'status' => $this->status->value,
            'core_compatibility' => $this->core_compatibility,
            'dependencies' => $this->getDependencies(),
            'installed_at' => $this->installed_at?->toIso8601String(),
            'enabled_at' => $this->enabled_at?->toIso8601String(),
            'tenant_id' => $this->tenant_id,
            'is_active' => $this->isActive(),
            'is_inactive' => $this->isInactive(),
            'is_installed' => $this->isInstalled(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
