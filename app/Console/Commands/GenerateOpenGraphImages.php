<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePackageOpenGraphImage;
use App\Package;
use Illuminate\Console\Command;

class GenerateOpenGraphImages extends Command
{
    protected $signature = 'generate:ogimage {package? : The ID of the package}';

    protected $description = 'Generates new Open Graph images for every package.';

    public function handle()
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
