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

    public function __construct(
        public Collaborator $collaborator,
        public User $user
    ) {
    }
}
