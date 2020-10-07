<?php

namespace App\Jobs;

use App\Tag;
use Exception;
use Zttp\Zttp;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

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

        $errorTag = $this->returnErrorTag();
        $this->package->tags()->syncWithoutDetaching($errorTag->id);
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
