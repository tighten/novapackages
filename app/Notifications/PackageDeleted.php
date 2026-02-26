<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\SectionBlock;
use Illuminate\Notifications\Slack\SlackMessage;

class PackageDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $packageName, public User $actor)
    {
        //
    }

    public function via($notifiable): array
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->text("Package {$this->packageName} was deleted")
            ->headerBlock('Package deleted')
            ->sectionBlock(function (SectionBlock $section) {
                $section->text(
                    "*{$this->packageName}* was deleted by {$this->actor->name} (user id: {$this->actor->id})"
                );
            });
    }
}
