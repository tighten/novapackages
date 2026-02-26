<?php

namespace App\Events;

use App\Models\Collaborator;
use App\Models\User;
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
