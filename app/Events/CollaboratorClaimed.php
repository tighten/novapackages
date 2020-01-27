<?php

namespace App\Events;

use App\Collaborator;
use App\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CollaboratorClaimed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $collaborator;

    public $user;

    public function __construct(Collaborator $collaborator, User $user)
    {
        $this->collaborator = $collaborator;
        $this->user = $user;
    }
}
