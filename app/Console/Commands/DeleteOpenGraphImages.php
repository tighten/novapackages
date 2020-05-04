<?php

namespace App\Console\Commands;

use App\Package;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOpenGraphImages extends Command
{
    protected $signature = 'purge:ogimage {package? : The ID of the package}';

    protected $description = 'Deletes all existing Open Graph images from storage.';

    public function handle()
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
