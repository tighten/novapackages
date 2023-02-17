<?php

namespace App;

use App\Exceptions\PackagistException;
use App\Http\Remotes\BitBucket;
use App\Http\Remotes\GitHub;
use App\Http\Remotes\GitLab;
use App\Http\Remotes\Npm;
use App\Http\Remotes\Packagist;

class Repo
{
    public function fromRequest($request)
    {
        try {
            return PackagistRepo::make($request->getComposerName());
        } catch (PackagistException $e) {
            if (stripos($e->getMessage(), 'package not found')) {
                return $this->fromUrl($request->input('url'));
            }

            throw $e;
        }
    }

    public function fromPackageModel($package)
    {
        try {
            return PackagistRepo::make($package->composer_name);
        } catch (PackagistException $e) {
            if (stripos($e->getMessage(), 'package not found')) {
                return $this->fromUrl($package->url);
            }

            throw $e;
        }
    }

    public function fromUrl($url)
    {
        return match (true) {
            GitHub::validateUrl($url) => GitHubRepo::make($url),
            BitBucket::validateUrl($url) => BitBucketRepo::make($url),
            GitLab::validateUrl($url) => GitLabRepo::make($url),
            Packagist::validateUrl($url) => PackagistRepo::makeFromUrl($url),
            Npm::validateUrl($url) => NpmRepo::make($url),
            default => BaseRepo::make($url),
        };
    }
}
