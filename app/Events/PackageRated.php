<?php

namespace App\Events;

use App\Package;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackageRated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $packageId;

    public $userId;

    public function __construct($packageId)
    {
        $this->packageId = $packageId;

        if (auth()->check()) {
            $this->userId = auth()->id();
        }
    }
}
