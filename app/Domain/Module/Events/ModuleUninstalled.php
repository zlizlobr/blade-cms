<?php

declare(strict_types=1);

namespace App\Domain\Module\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleUninstalled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $slug
    ) {}
}
