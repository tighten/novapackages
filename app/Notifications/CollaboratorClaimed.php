<?php

namespace App\Notifications;

use App\Collaborator;
use App\Events\CollaboratorClaimed as CollaboratorClaimedEvent;
use App\Tighten;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\SectionBlock;
use Illuminate\Notifications\Slack\SlackMessage;

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

    public function via($notifiable): array
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->text('Collaborator claimed!')
            ->headerBlock('Collaborator claimed!')
            ->sectionBlock(function (SectionBlock $section) {
                $section->text(
                    "*Collaborator:* {$this->collaborator->name}\n"
                    ."*User:* {$this->user->name}"
                );
            });
    }
}
