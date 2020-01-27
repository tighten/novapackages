<?php

namespace App;

use App\BaseRepo;
use App\Http\Remotes\Packagist;
use Facades\App\Repo;
use Illuminate\Support\Arr;

class PackagistRepo extends BaseRepo
{
    protected $url;

    protected $packagist;

    protected $repo;

    private function __construct($name, Packagist $packagist)
    {
        $this->packagist = $packagist;
        $this->url = "https://packagist.org/packages/{$name}";
        $this->initializeRepo();
    }

    public static function make($composerName)
    {
        return new static($composerName, new Packagist($composerName));
    }

    public static function makeFromUrl($url)
    {
        preg_match('/packagist\.org\/packages\/([\w-]+)\/([\w-]+)/i', $url, $parts);
        $composerName = $parts[1].'/'.$parts[2];

        return self::make($composerName);
    }

    public function initializeRepo()
    {
        $this->repo = Repo::fromUrl($this->url());
    }

    public function repo()
    {
        return $this->repo;
    }

    public function url()
    {
        return ($this->repo)
            ? $this->repo->url()
            : Arr::get($this->packagist->data(), 'package.repository');
    }

    public function latestReleaseVersion()
    {
        return $this->repo->latestReleaseVersion()
            ?? key(array_slice(Arr::get($this->packagist->data(), 'package.versions', []), 1, 1))
            ?? 'master';
    }

    public function readme()
    {
        return $this->repo->readme();
    }
}
