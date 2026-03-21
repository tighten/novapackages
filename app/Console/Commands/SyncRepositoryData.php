<?php

namespace App\Console\Commands;

use App\Jobs\SyncPackageRepositoryData;
use App\Models\Package;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('sync:repo {package? : The ID of the package}')]
#[Description('Sync VCS repository readme, url and source for every package.')]
class SyncRepositoryData extends Command
{
    public function handle(): void
    {
        $packages = $this->argument('package')
            ? Package::where('id', $this->argument('package'))->get()
            : Package::all();

        foreach ($packages as $package) {
            dispatch(new SyncPackageRepositoryData($package));
        }
    }
}
