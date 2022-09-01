<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PackageDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public string $packageName, public User $user)
    {
        //
    }
}
