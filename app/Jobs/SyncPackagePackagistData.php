<?php

namespace App\Jobs;

use App\Exceptions\PackagistException;
use App\Http\Remotes\Packagist;
use App\Models\Package;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class SyncPackagePackagistData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $package;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($package)
    {
        $this->package = $package;
    }

    public static function parseNovaVersion(?string $constraint): ?int
    {
        if (! $constraint) {
            return null;
        }

        // Split on | to handle multiple version constraints like "^4.0|^5.0"
        $parts = explode('|', $constraint);
        $majorVersions = [];

        foreach ($parts as $part) {
            if (preg_match('/(\d+)/', trim($part), $matches)) {
                $majorVersions[] = (int) $matches[1];
            }
        }

        return ! empty($majorVersions) ? max($majorVersions) : null;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $packagistData = Packagist::make($this->package->composer_name)->data();

            $novaVersion = null;

            if (! is_null($packagistData)) {
                $composerLatest = $this->extractStableVersionsFromPackages($packagistData)->first();

                $novaConstraint = $composerLatest['require']['laravel/nova'] ?? null;
                $novaVersion = self::parseNovaVersion($novaConstraint);
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
                'is_abandoned' => (bool) Arr::get($packagistData, 'package.abandoned', false),
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
