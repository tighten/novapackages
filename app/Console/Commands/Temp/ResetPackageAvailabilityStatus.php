<?php

namespace App\Console\Commands\Temp;

use App\Package;
use Illuminate\Console\Command;

class ResetPackageAvailabilityStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'novapackages:reset-package-availability';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset package availability status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $packages = Package::withoutGlobalScopes()->get();
        $packages->reject(function($package) {
                return is_null($package->marked_as_unavailable_at);
            })
            ->each(function($package) {
                $package->marked_as_unavailable_at = null;
                $package->save();
                $this->info("Resetting status for package ID #{$package->id}, {$package->name}");
            });
    }
}
