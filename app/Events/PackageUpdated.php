<?php

namespace App\Events;

use App\Models\Package;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackageUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Package $package)
    {
    }
}
