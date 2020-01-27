<?php

namespace App\Jobs;

use Exception;
use Facades\App\Repo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncPackageRepositoryData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $package;

    public function __construct($package)
    {
        $this->package = $package;
    }

    public function handle()
    {
        try {
            $repo = Repo::fromPackageModel($this->package);
        } catch (Exception $e) {
            return;
        }

        if (! $this->remoteHasChanges($repo)) {
            return Log::info('Repository data is unchanged for package #'.$this->package->id.' ('.$this->package->name.')');
        }

        $this->package->update([
            'repo_url' => $repo->url(),
            'readme_source' => $repo->source(),
            'readme' => $repo->readme(),
            'readme_format' => $repo->readmeFormat(),
            'latest_version' => $repo->latestReleaseVersion(),
        ]);

        Log::info('Synced repository data for package #'.$this->package->id.' ('.$this->package->name.')');
    }

    protected function remoteHasChanges($repo)
    {
        return $repo->url() != $this->package->repo_url
            || $repo->source() != $this->package->readme_source
            || $repo->readme() != $this->package->readme
            || $repo->readmeFormat() != $this->package->readme_format
            || $repo->latestReleaseVersion() != $this->package->latest_version;
    }
}
