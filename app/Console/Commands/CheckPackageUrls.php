<?php

namespace App\Console\Commands;

use App\Package;
use Illuminate\Console\Command;
use App\Jobs\CheckPackageUrls as CheckPackageUrlsJob;
use Zttp\Zttp;

class CheckPackageUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:package-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check all package URLs for 404 errors';

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
     * @return mixed
     */
    public function handle()
    {
        $validPackages = Package::whereHas('tags', function ($query) {
            $query->where('name', '!=', '404 error');
        })
        ->orWhereDoesntHave('tags')
        ->with(['author', 'contributors'])
        ->get();

        foreach ($validPackages as $package) {
            dispatch(new CheckPackageUrlsJob($package));
        }
    }
}
