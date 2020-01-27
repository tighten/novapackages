<?php

namespace App\Events;

use App\Collaborator;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CollaboratorCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $collaborator;

    public function __construct(Collaborator $collaborator)
    {
        $this->collaborator = $collaborator;
    }
}
