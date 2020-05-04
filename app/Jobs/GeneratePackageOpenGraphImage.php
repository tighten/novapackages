<?php

namespace App\Jobs;

use App\OpenGraphImage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GeneratePackageOpenGraphImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $packageName;
    protected $packageAuthor;
    protected $packageOgImageName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $packageName, string $packageAuthor, string $packageOgImageName)
    {
        $this->packageName = $packageName;
        $this->packageAuthor = $packageAuthor;
        $this->packageOgImageName = $packageOgImageName;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ogi = new OpenGraphImage($this->packageOgImageName);

        if (! $ogi->makeStorageDirectory()) {
            $ogi->removeDuplicates();
        }

        $image = $ogi->make(
            $this->packageName,
            "By {$this->packageAuthor}",
            public_path('images/package-opengraph-base.png')
        );

        $ogi->save($image);
    }
}
