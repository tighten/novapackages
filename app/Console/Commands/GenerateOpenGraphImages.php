<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePackageOpenGraphImage;
use App\Models\Package;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('generate:ogimage {package? : The ID of the package}')]
#[Description('Generates new Open Graph images for every package.')]
class GenerateOpenGraphImages extends Command
{
    public function handle(): void
    {
        $this->callSilent('purge:ogimage', ['package' => $this->argument('package')]);

        $packages = $this->argument('package')
            ? Package::where('id', $this->argument('package'))->get()
            : Package::all();

        $bar = $this->output->createProgressBar(count($packages));
        $this->info('Generating images ...');
        $bar->start();

        foreach ($packages as $package) {
            dispatch(new GeneratePackageOpenGraphImage(
                $package->name,
                $package->author->name,
                $package->og_image_name,
            ));
            $bar->advance();
        }

        $bar->finish();
    }
}
