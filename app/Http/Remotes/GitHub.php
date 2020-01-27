<?php

namespace App\Http\Remotes;

use App\CacheKeys;
use Github\Client as GitHubClient;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class GitHub
{
    protected $github;

    public function __construct(GitHubClient $github)
    {
        $this->github = $github;
    }

    /**
     * Return user by username.
     *
     * @param  string $username GitHub username
     * @return array            User associative array
     */
    public function user($username)
    {
        return $this->github->api('user')->show($username);
    }

    /**
     * Get all issues labeled "suggestion".
     *
     * @return array of items
     */
    public function packageIdeaIssues()
    {
        return Cache::remember(CacheKeys::packageIdeaIssues(), 1, function () {
            $issues = collect($this->github->api('search')->issues('state:open label:package-idea repo:tightenco/nova-package-development')['items']);

            return $this->sortIssuesByPositiveReactions($issues);
        });
    }

    protected function sortIssuesByPositiveReactions($issues)
    {
        return $issues->sortByDesc(function ($issue) {
            $countReactionTypes = collect($issue['reactions'])
                ->except(['url', 'total_count'])
                ->filter()
                ->count();

            return $countReactionTypes
             + Arr::get($issue, 'reactions.total_count')
             - (2 * Arr::get($issue, 'reactions.-1'))
             - Arr::get($issue, 'reactions.confused');
        });
    }

    public function api($api)
    {
        return $this->github->api($api);
    }

    public static function validateUrl($url)
    {
        return (bool) preg_match('/github.com\/([\w-]+)\/([\w-]+)/i', $url);
    }
}
