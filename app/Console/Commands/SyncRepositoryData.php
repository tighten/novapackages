<?php

namespace App\Console\Commands;

use App\Jobs\SyncPackageRepositoryData;
use App\Package;
use Illuminate\Console\Command;

class SyncRepositoryData extends Command
{
    protected $signature = 'sync:repo {package? : The ID of the package}';

    protected $description = 'Sync VCS repository readme, url and source for every package.';

    public function handle()
    {
        $packages = $this->argument('package')
            ? Package::where('id', $this->argument('package'))->get()
            : Package::all();

        foreach ($packages as $package) {
            dispatch(new SyncPackageRepositoryData($package));
        }
    }
}
