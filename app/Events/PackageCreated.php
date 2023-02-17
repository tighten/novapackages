<?php

namespace App\Events;

use App\Package;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackageCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Package $package)
    {
    }
}
