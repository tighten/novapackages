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

    protected $github;

    protected $username;

    protected $repo;

    private function __construct($url, GitHub $github)
    {
        if (! $github->validateUrl($url)) {
            throw new GitHubException('Invalid Url Provided');
        }

        $this->url = $url;
        $this->github = $github->api('repo');

        preg_match('/github.com\/([\w-]+)\/([\w-]+)/i', $url, $parts);
        $this->username = $parts[1];
        $this->repo = $parts[2];
    }

    public static function make($url)
    {
        return new static($url, app(GitHub::class));
    }

    // Media types for GitHub: https://developer.github.com/v3/media
    public function readme($format = 'html')
    {
        // @todo: handle exceptions. May throw an exception if readme does not exist.
        // If the exception is a 404, return null. Otherwise, re-throw the exception.
        return Http::github()
            ->withHeaders(['Accept' => "application/vnd.github.{$format}"])
            ->get("/repos/{$this->username}/{$this->repo}/readme")
            ->body();
    }

    public function releases()
    {
        // @todo: handle exceptions.
        // If the exception is a "Not Found", return an empty array. Otherwise, re-throw the exception.
        return Http::github()
            ->get("/repos/{$this->username}/{$this->repo}/releases")
            ->json();
    }

    public function latestRelease()
    {
        return $this->releases()[0];
    }

    public function latestReleaseVersion()
    {
        // For GitHub, we want the `tag_name` of the latest release (rather than the `name`)
        // in order to correctly format relative URLs in the readme
        return Arr::get($this->latestRelease(), 'tag_name', 'master');
    }
}
