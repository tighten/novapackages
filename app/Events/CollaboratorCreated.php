<?php

namespace App\Events;

use App\Collaborator;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CollaboratorCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Collaborator $collaborator)
    {
    }
}
