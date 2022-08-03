<?php

namespace App;

use App\BaseRepo;
use App\Exceptions\GitHubException;
use App\Http\Remotes\GitHub;
use Illuminate\Support\Arr;

class GitHubRepo extends BaseRepo
{
    const SOURCE = 'github';

    const README_FORMAT = 'html';

    protected $url;

    protected $github;

    protected $username;

    protected $repo;

    private array $cachedResponses;

    private function __construct($url, GitHub $github)
    {
        if (! GitHub::validateUrl($url)) {
            throw new GitHubException('Invalid Url Provided');
        }

        $this->url = $url;
        $this->github = $github;

        preg_match('/github.com\/([\w-]+)\/([\w-]+)/i', $url, $parts);
        $this->username = $parts[1];
        $this->repo = $parts[2];
    }

    public static function make($url)
    {
        return new static($url, app(GitHub::class));
    }

    public function readme()
    {
        return $this->cached('readme', fn() => $this->github->readme("{$this->username}/{$this->repo}"));
    }

    public function releases()
    {
        return $this->cached('releases', fn() => collect($this->github->releases("{$this->username}/{$this->repo}")));
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

    private function cached(string $key, callable $callback)
    {
        if (isset($this->cachedResponses[$key])) {
            return $this->cachedResponses[$key];
        }

        $result = $callback();

        $this->cachedResponses[$key] = $result;

        return $result;
    }
}
