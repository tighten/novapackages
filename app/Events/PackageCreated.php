<?php

namespace App\Events;

use App\Models\Package;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackageCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $package;

    public function __construct(Package $package)
    {
        $this->package = $package;
    }
}
