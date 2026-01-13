<?php

declare(strict_types=1);

namespace App\Domain\Module\Events;

use App\Domain\Module\Models\Module;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModuleDeactivated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Module $module
    ) {}
}
