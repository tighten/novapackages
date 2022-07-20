<?php

namespace App;

use App\BaseRepo;
use App\Exceptions\GitHubException;
use App\Http\Remotes\GitHub;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class GitHubRepo extends BaseRepo
{
    const SOURCE = 'github';

    const README_FORMAT = 'html';

    protected $url;

    protected $username;

    protected $repo;

    private function __construct($url)
    {
        if (! GitHub::validateUrl($url)) {
            throw new GitHubException('Invalid Url Provided');
        }

        $this->url = $url;

        preg_match('/github.com\/([\w-]+)\/([\w-]+)/i', $url, $parts);
        $this->username = $parts[1];
        $this->repo = $parts[2];
    }

    public static function make($url)
    {
        return new static($url);
    }

    // Media types for GitHub: https://developer.github.com/v3/media
    public function readme()
    {
        // @todo: handle exceptions
        $response = Http::github()
            ->withHeaders(['Accept' => 'application/vnd.github.html'])
            ->get("/repos/{$this->username}/{$this->repo}/readme");

        if ($response->status() === 404) {
            return null;
        }

        return $response->body();
    }

    public function releases()
    {
        // @todo: handle exceptions
        return Http::github()
            ->withHeaders(['Accept' => 'application/vnd.github+json'])
            ->get("/repos/{$this->username}/{$this->repo}/releases")
            ->collect();
    }

    public function latestRelease()
    {
        return $this->releases()->first();
    }

    public function latestReleaseVersion()
    {
        // For GitHub, we want the `tag_name` of the latest release (rather than the `name`)
        // in order to correctly format relative URLs in the readme
        return Arr::get($this->latestRelease(), 'tag_name', 'master');
    }
}
