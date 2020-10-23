<?php

namespace App\Jobs;

use App\Notifications\NotifyContributorOfInvalidPackageUrl;
use App\Tag;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Zttp\Zttp;

class CheckPackageUrls implements ShouldQueue
{

    private $package;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct($package)
    {
        $this->package = $package;
    }

    public function handle()
    {
        $urlIsValid = true;
        try {
            $status = Zttp::get($this->package->url);
            if ($status->isSuccess()) return;
            if ($status->isServerError()) return; // if 500, issue probably isn't with URL
            $urlIsValid = false;
        } catch (Exception $e) {
            $urlIsValid = false;
        }

        if ($urlIsValid) return;

        $this->package->tags()->syncWithoutDetaching($this->fetchErrorTagId());

        if ($this->package->author && $this->package->authorIsUser()) {
            $this->package->author->user->notify(new NotifyContributorOfInvalidPackageUrl($this->package));
        }

        foreach ($this->package->contributors as $contributor) {
            if (!$contributor->user) return;
            $contributor->user->notify(new NotifyContributorOfInvalidPackageUrl($this->package));
        }
    }

    /**
     * Find or create 404 tag, and return tag ID
     * @return int
     */
    private function fetchErrorTagId()
    {
        $errorTag = Tag::where('name', '404 error')
            ->first();
        if ($errorTag) return $errorTag->id;

        return Tag::create([
            'name' => '404 error',
            'slug' => '404-error'
        ])->id;
    }
}
