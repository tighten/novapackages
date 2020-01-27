<?php

namespace App;

use App\BaseRepo;
use App\Exceptions\GitHubException;
use App\Http\Remotes\GitHub;
use Exception;
use Github\Exception\RuntimeException as GitHubRunTimeException;
use Illuminate\Support\Arr;

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
        // Github throws an exception if a readme doesn't exist for the repo so we catch it and return null
        try {
            return $this->github->readme(
                $this->username,
                $this->repo,
                $format
            );
        } catch (Exception $e) {
            return;
        }
    }

    public function releases()
    {
        return $this->github->releases();
    }

    public function latestRelease()
    {
        try {
            return $this->releases()->all($this->username, $this->repo)[0] ?? [];
        } catch (GitHubRunTimeException $e) {
            if ($e->getMessage() == 'Not Found') {
                return [];
            }

            throw $e;
        }
    }

    public function latestReleaseVersion()
    {
        // For GitHub, we want the `tag_name` of the latest release (rather than the `name`)
        // in order to correctly format relative URLs in the readme
        return Arr::get($this->latestRelease(), 'tag_name', 'master');
    }
}
