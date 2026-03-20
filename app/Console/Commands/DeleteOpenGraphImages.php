<?php

namespace App\Console\Commands;

use App\Models\Package;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

#[Signature('purge:ogimage {package? : The ID of the package}')]
#[Description('Deletes all existing Open Graph images from storage.')]
class DeleteOpenGraphImages extends Command
{
    public function handle(): void
    {
        if (! $this->argument('package')) {
            $files = Storage::allFiles(config('opengraph.image_directory_name') . '/');
            Storage::delete($files);

            return;
        }

        $package = Package::where('id', $this->argument('package'))->first();
        $file = config('opengraph.image_directory_name') . "/{$package->og_image_name}";

        Storage::delete($file);
    }
}
