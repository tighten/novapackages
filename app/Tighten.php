<?php

namespace App;

use Illuminate\Notifications\Notifiable;

/**
 * A non-eloquent notifiable model to facilitate notifications to Tighten Slack.
 */
class Tighten
{
    use Notifiable {
        notify as traitNotify;
    }

    public function routeNotificationForSlack($notification)
    {
        return config('services.slack.webhook_url');
    }

    public function notify($instance)
    {
        if (! config('services.slack.webhook_url')) {
            return;
        }

        $this->traitNotify($instance);
    }

    public function getKey()
    {
        return 'This is a method just for Notification::fake() 😢';
    }
}
