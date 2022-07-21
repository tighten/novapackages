<?php

namespace App\Http\Remotes;

use App\CacheKeys;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GitHub
{
    public static function validateUrl($url): bool
    {
        return (bool) preg_match('/^https?:\/\/github.com\/([\w-]+)\/([\w-]+)/i', $url);
    }

    /**
     * Get all issues labeled "suggestion".
     *
     * @return array of items
     */
    public function packageIdeaIssues()
    {
        return Cache::remember(CacheKeys::packageIdeaIssues(), 1, function () {
            // @todo: handle exceptions
            $issues = Http::github()
                ->withHeaders(['Accept' => 'application/vnd.github+json'])
                ->get('/search/issues', [
                    'q' => 'state:open label:package-idea repo:tighten/nova-package-development',
                    'sort' => 'updated',
                    'order' => 'desc',
                ])
                ->json();

            return $this->sortIssuesByPositiveReactions($issues['items']);
        });
    }

    /**
     * Return user by username.
     *
     * @param string $username GitHub username
     * @return array            User associative array
     */
    public function user($username)
    {
        // @todo: handle exceptions
        return Http::github()->get("/users/{$username}")->json();
    }

    public function readme(string $repository): string|null
    {
        // @todo: handle exceptions
        $response = Http::github()
            ->withHeaders(['Accept' => 'application/vnd.github.html'])
            ->get("/repos/{$repository}/readme");

        if ($response->status() === 404) {
            return null;
        }

        return $response->body();
    }

    public function releases(string $repository): array
    {
        // @todo: handle exceptions
        return Http::github()
            ->withHeaders(['Accept' => 'application/vnd.github+json'])
            ->get("/repos/{$repository}/releases")
            ->json();
    }

    private function sortIssuesByPositiveReactions($issues)
    {
        return collect($issues)->sortByDesc(function ($issue) {
            $countReactionTypes = collect($issue['reactions'])
                ->except(['url', 'total_count'])
                ->filter()
                ->count();

            return $countReactionTypes
                + Arr::get($issue, 'reactions.total_count')
                - (2 * Arr::get($issue, 'reactions.-1'))
                - Arr::get($issue, 'reactions.confused');
        })->values();
    }
}
