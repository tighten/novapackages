<?php

namespace App;

use App\BaseRepo;
use App\Http\Remotes\Npm;
use Facades\App\Repo;
use Illuminate\Support\Arr;

class NpmRepo extends BaseRepo
{
    const SOURCE = 'npm';

    protected $url;

    protected $npm;

    protected $repo;

    private function __construct($url, Npm $npm)
    {
        $this->url = $url;
        $this->npm = $npm;
        $this->initializeRepo();
    }

    public static function make($url)
    {
        return new static($url, new Npm($url));
    }

    public function initializeRepo()
    {
        if ($this->hasRepo()) {
            $this->repo = Repo::fromUrl($this->url());
        }
    }

    protected function hasRepo()
    {
        return Arr::get($this->npm->data(), 'repository.url');
    }

    public function repo()
    {
        return $this->repo;
    }

    public function url()
    {
        if ($this->repo) {
            return $this->repo->url();
        }

        // On occasion, NPM will not provide a repository url for a package. In this case we use
        // the NPM registry url as the project's repo url.
        if (Arr::get($this->npm->data(), 'repository.url')) {
            list($sourceControlType, $repoUrl) = explode('+', Arr::get($this->npm->data(), 'repository.url'), 2);
        } else {
            $repoUrl = $this->url;
        }

        return $repoUrl;
    }

    public function latestReleaseVersion()
    {
        return optional($this->repo)->latestReleaseVersion() ?? 'master';
    }

    public function readme()
    {
        return ($this->repo)
            ? $this->repo->readme()
            : Arr::get($this->npm->data(), 'readme');
    }
}
