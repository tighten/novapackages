<?php

namespace App\Notifications;

use App\Collaborator;
use App\Events\CollaboratorClaimed as CollaboratorClaimedEvent;
use App\Tighten;
use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class CollaboratorClaimed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $collaborator;

    protected $user;

    public function __construct(Collaborator $collaborator, User $user)
    {
        $this->collaborator = $collaborator;
        $this->user = $user;
    }

    public function handle(CollaboratorClaimedEvent $event)
    {
        $this->collaborator = $event->collaborator;
        $this->user = $event->user;

        (new Tighten)->notify($this);
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->success()
            ->content('Collaborator claimed!')
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->fields([
                        'Collaborator Name' => $this->collaborator->name,
                        'User Name' => $this->user->name,
                    ])
                    ->timestamp(Carbon::now());
            });
    }
}
