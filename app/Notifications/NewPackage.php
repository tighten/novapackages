<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackAttachment;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class NewPackage extends Notification implements ShouldQueue
{
    use Queueable;

    protected $package;

    protected $fields;

    public function __construct($package)
    {
        $this->package = $package;
        $this->fields = ['URL' => $this->package->url];

        if (! isset($this->package->author->user) || Auth::id() != $this->package->author->user->id) {
            $this->fields['Created By'] = Auth::user()->name;
        }
    }

    public function via($notifiable)
    {
        return ['slack'];
    }

    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content('New package created!')
            ->attachment(function (SlackAttachment $attachment) {
                $attachment
                    ->color('#41ac9c')
                    ->title($this->package->name, route('packages.show', [
                        'namespace' => $this->package->composer_vendor,
                        'name' => $this->package->composer_package,
                    ]))
                    ->author($this->package->author->name, null, $this->package->author->avatar)
                    ->content('`'.$this->package->composer_name."`\n\n".$this->package->abstract)
                    ->image($this->package->picture_url)
                    ->fields($this->fields)
                    ->timestamp(Carbon::now());
            });
    }
}
