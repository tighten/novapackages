<?php

namespace App\Listeners;

use App\Collaborator;
use App\Events\NewUserSignedUp;
use App\Http\Remotes\GitHub;
use App\Notifications\CollaboratorClaimed;
use App\Tighten;

class ClaimOrCreateCollaboratorForNewUser
{
    public function handle(NewUserSignedUp $event)
    {
        if (Collaborator::where('github_username', $event->user->github_username)->count() > 0) {
            return $this->claimCollaborator($event);
        }

        return $this->createCollaborator($event);
    }

    private function claimCollaborator($event)
    {
        $collaborator = Collaborator::where('github_username', $event->user->github_username)->first();
        $collaborator->user_id = $event->user->id;
        $collaborator->save();

        (new Tighten)->notify(new CollaboratorClaimed($collaborator, $event->user));
    }

    private function createCollaborator($event)
    {
        $githubUser = app(GitHub::class)->user($event->user->github_username);

        Collaborator::create([
            'user_id' => $event->user->id,
            'name' => $event->user->name,
            'github_username' => $event->user->github_username,
            'url' => '',
            'description' => '',
            'avatar' => $githubUser['avatar_url'] ?? null,
        ]);
    }
}
