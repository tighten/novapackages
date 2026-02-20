<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\SectionBlock;
use Illuminate\Notifications\Slack\SlackMessage;
use Illuminate\Support\Facades\Auth;

class NewPackage extends Notification implements ShouldQueue
{
    use Queueable;

    protected $package;

    public function __construct($package)
    {
        $this->package = $package;
    }

    public function via($notifiable): array
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        $packageUrl = route('packages.show', [
            'namespace' => $this->package->composer_vendor,
            'name' => $this->package->composer_package,
        ]);

        $createdBy = '';
        if (! isset($this->package->author->user) || Auth::id() != $this->package->author->user->id) {
            $createdBy = "\nCreated by: " . Auth::user()->name;
        }

        return (new SlackMessage)
            ->text('New package created!')
            ->headerBlock('New package created!')
            ->sectionBlock(function (SectionBlock $section) use ($packageUrl, $createdBy) {
                $section->text(
                    "*<{$packageUrl}|{$this->package->name}>*\n"
                    . "by {$this->package->author->name}\n"
                    . "`{$this->package->composer_name}`\n\n"
                    . $this->package->abstract
                    . $createdBy
                );
            });
    }
}
