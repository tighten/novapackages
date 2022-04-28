<?php

namespace App\Jobs;

use App\Exceptions\PackagistException;
use App\Http\Remotes\Packagist;
use App\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SyncPackagePackagistData implements ShouldQueue
{
    private $package;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($package)
    {
        $this->package = $package;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $packagistData = Packagist::make($this->package->composer_name)->data();

            $novaVersion = null;

            if (! is_null($packagistData)) {
                $composerLatest = $this->extractStableVersionsFromPackages($packagistData)->first();

                $novaVersion = $composerLatest['require']['laravel/nova'] ?? null;

                // Filter version numbers
                $novaVersion = preg_replace('/[^0-9]/', '', $novaVersion);

                if (strlen($novaVersion) > 0) {
                    $novaVersion = substr($novaVersion, 0, 1);
                }else {
                    $novaVersion = null;
                }
            }
        } catch (PackagistException $e) {
            return;
        }

        if (! $packagistData) {
            return;
        }

        Package::withoutSyncingToSearch(function () use ($packagistData, $novaVersion) {
            $this->package->update([
                'packagist_downloads' => Arr::get($packagistData, 'package.downloads.total', 0) ?: 0,
                'github_stars' => Arr::get($packagistData, 'package.github_stars', 0) ?: 0,
                'repo_url' => $packagistData['package']['repository'],
                'nova_version' => $novaVersion,
            ]);
        });

        Log::info('Synced packagist data for package #' . $this->package->id . ' (' . $this->package->name . ')');
    }

    private function extractStableVersionsFromPackages($packagist)
    {
        return collect($packagist['package']['versions'])->reject(function ($version) {
            return strpos($version['version'], 'dev') !== false;
        });
    }
}
