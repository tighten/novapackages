<?php

namespace App\Http\Remotes;

use App\CacheKeys;
use App\Exceptions\GitHubException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GitHub
{
    public static function validateUrl($url): bool
    {
        return (bool) preg_match('/^https?:\/\/github.com\/([\w-]+)\/([\w-]+)/i', $url);
    }

    public function packageIdeaIssues(): Collection
    {
        return Cache::remember(CacheKeys::packageIdeaIssues(), 1, function () {
            $issues = Http::github()
                ->accept('application/vnd.github+json')
                ->get('search/issues', [
                    'q' => 'state:open label:package-idea repo:tighten/nova-package-development',
                    'sort' => 'updated',
                    'order' => 'desc',
                ])
                ->throw()
                ->json();

            return $this->sortIssuesByPositiveReactions($issues['items']);
        });
    }

    public function readme(string $repositoryPath): string|null
    {
        $this->guardAgainstInvalidRepositoryPath($repositoryPath);

        $response = Http::github()
            ->accept('application/vnd.github.html')
            ->get("repos/{$repositoryPath}/readme");

        if ($response->status() === 404) {
            return null;
        }

        $response->throw();

        return $response->body();
    }

    public function releases(string $repositoryPath): array
    {
        $this->guardAgainstInvalidRepositoryPath($repositoryPath);

        $response = Http::github()
            ->accept('application/vnd.github+json')
            ->get("repos/{$repositoryPath}/releases");

        if ($response->status() === 404) {
            return [];
        }

        $response->throw();

        return $response->json();
    }

    public function user(string $username): array
    {
        return Http::github()->get("users/{$username}")->throw()->json();
    }

    private function sortIssuesByPositiveReactions(array $issues): Collection
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

    private function guardAgainstInvalidRepositoryPath(string $repositoryPath): void
    {
        if (! preg_match('/^([\w-]+)\/([\w-]+)/', $repositoryPath)) {
            throw new GitHubException("Invalid repository path provided: {$repositoryPath}");
        }
    }
}
