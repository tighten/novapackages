<?php

namespace App;

use App\BaseRepo;
use App\BitBucketRepo;
use App\Exceptions\PackagistException;
use App\GitHubRepo;
use App\GitLabRepo;
use App\Http\Remotes\BitBucket;
use App\Http\Remotes\GitHub;
use App\Http\Remotes\GitLab;
use App\Http\Remotes\Npm;
use App\Http\Remotes\Packagist;
use App\NpmRepo;
use App\PackagistRepo;

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
        switch (true) {
            case GitHub::validateUrl($url):
                return GitHubRepo::make($url);
            case BitBucket::validateUrl($url):
                return BitBucketRepo::make($url);
            case GitLab::validateUrl($url):
                return GitLabRepo::make($url);
            case Packagist::validateUrl($url):
                return PackagistRepo::makeFromUrl($url);
            case Npm::validateUrl($url):
                return NpmRepo::make($url);
            default:
                return BaseRepo::make($url);
        }
    }
}
