<?php

namespace App\Jobs;

use App\Tag;
use Exception;
use Zttp\Zttp;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Notifications\NotifyContributorOfInvalidPackageUrl;

class CheckPackageUrls implements ShouldQueue
{

    private $package;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($package)
    {
        $this->package = $package;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $urlsAreInvalid = collect([
            $this->package->url,
            $this->package->repo_url,
        ])->contains(function ($url) {
            try {
                return Zttp::get($url)->status() != 200;
            } catch (Exception $e) {
                return true; // If domain can't be reached, confirm URL is invalid
            }
        });
        if (!$urlsAreInvalid) return;

        // Attach error tag
        $errorTag = $this->returnErrorTag();
        $this->package->tags()->syncWithoutDetaching($errorTag->id);

        // Notify authors and contributors
        if ($this->package->author && $this->package->authorIsUser()) {
            $this->package->author->user->notify(new NotifyContributorOfInvalidPackageUrl($this->package));
        }

        foreach ($this->package->contributors as $contributor) {
            if (!$contributor->user) return;
            $contributor->user->notify(new NotifyContributorOfInvalidPackageUrl($this->package));
        }
    }

    /**
     * Find or create 404 tag
     * @return Tag
     */
    private function returnErrorTag()
    {
        $errorTag = Tag::where('name', '404 error')
            ->first();
        if ($errorTag) return $errorTag;

        return Tag::create([
            'name' => '404 error',
            'slug' => '404-error'
        ]);
    }
}
