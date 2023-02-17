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

    public function __construct(
        protected string $packageName,
        protected string $packageAuthor,
        protected string $packageOgImageName,
    ) {
    }

    public function handle()
    {
        (new OpenGraphImage($this->packageName, $this->packageAuthor, $this->packageOgImageName))->generate();
    }
}
