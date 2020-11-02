<?php

namespace App\Jobs;

use App\Notifications\NotifyAuthorOfUnavailablePackageUrl;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class CheckPackageUrlsForAvailability implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $package;

    public function __construct($package)
    {
        $this->package = $package;
    }

    public function handle()
    {
        $urlIsValid = $this->urlIsAccessible();

        if ($urlIsValid) {
            return;
        }

        $this->package->update(['marked_as_unavailable_at' => now()]);

        if ($this->package->author && $this->package->authorIsUser()) {
            $this->package->author->user->notify(new NotifyAuthorOfUnavailablePackageUrl($this->package));
        }
    }

    protected function urlIsAccessible()
    {
        try {
            $response = Http::get($this->package->url);

            // @todo: ensure we're only returning false if the package is actually missing; for other errors (e.g. GitHub is down or rate limit) delay the job
            if ($response->clientError()) {
                return false;
            }
        } catch (Exception $e) {
            // @todo: ensure we're only returning false if the package is actually missing; for other errors (e.g. GitHub is down or rate limit) delay the job
            return false;
        }

        return true;
    }
}
