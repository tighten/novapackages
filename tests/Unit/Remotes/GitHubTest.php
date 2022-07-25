<?php

namespace Tests\Unit\Remotes;

use App\Exceptions\GitHubException;
use App\Http\Remotes\GitHub;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GitHubTest extends TestCase
{
    /** @test */
    function validates_github_urls()
    {
        $this->assertTrue(GitHub::validateUrl('http://github.com/starwars/lightsabers'));
        $this->assertTrue(GitHub::validateUrl('https://github.com/starwars/lightsabers'));

        $this->assertFalse(GitHub::validateUrl('https://subdomain.github.com/starwars/lightsabers'));
        $this->assertFalse(GitHub::validateUrl('https://notgithub.com/starwars/lightsabers'));
    }

    /** @test */
    function requesting_package_idea_issues_sends_request_to_correct_url()
    {
        Http::fake([
            'https://api.github.com/search/issues*' =>
                Http::response($this->fakeResponse('github.search-issues-in-repo.json')),
        ]);

        app(GitHub::class)->packageIdeaIssues();

        Http::assertSent(function ($request) {
            $url = $request->url();
            return str_contains($url, 'https://api.github.com/search/issues')
                && str_contains($url, urlencode('repo:tighten/nova-package-development'));
        });
    }

    /** @test */
    function requesting_package_idea_issues_throws_exception_when_request_has_error()
    {
        Http::fake(['https://api.github.com/search/issues*' => Http::response(null, 500)]);

        $this->expectException(RequestException::class);

        app(GitHub::class)->packageIdeaIssues();
    }

    /** @test */
    function requesting_readme_requires_owner_and_repository()
    {
        $this->expectException(GitHubException::class);

        app(GitHub::class)->readme('invalid');
    }

    /** @test */
    function requesting_readme_sends_request_to_correct_url()
    {
        Http::fake(['https://api.github.com/repos/starwars/lightsabers/readme' => Http::response()]);

        app(GitHub::class)->readme('starwars/lightsabers');

        Http::assertSent(
            fn($request) => $request->url() === 'https://api.github.com/repos/starwars/lightsabers/readme'
        );
    }

    /** @test */
    function requesting_readme_returns_null_when_readme_does_not_exist()
    {
        Http::fake(['https://api.github.com/repos/starwars/lightsabers/readme' => Http::response(null, 404)]);

        $response = app(GitHub::class)->readme('starwars/lightsabers');

        $this->assertNull($response);
    }

    /** @test */
    function requesting_readme_throws_exception_when_request_has_error()
    {
        Http::fake(['https://api.github.com/repos/starwars/lightsabers/readme' => Http::response(null, 500)]);

        $this->expectException(RequestException::class);

        app(GitHub::class)->readme('starwars/lightsabers');
    }

    /** @test */
    function requesting_releases_requires_owner_and_repository()
    {
        $this->expectException(GitHubException::class);

        app(GitHub::class)->releases('invalid');
    }

    /** @test */
    function requesting_releases_sends_request_to_correct_url()
    {
        Http::fake(['https://api.github.com/repos/starwars/lightsabers/releases' => Http::response([])]);

        app(GitHub::class)->releases('starwars/lightsabers');

        Http::assertSent(
            fn($request) => $request->url() === 'https://api.github.com/repos/starwars/lightsabers/releases'
        );
    }

    /** @test */
    function requesting_releases_throws_exception_when_request_has_error()
    {
        Http::fake(['https://api.github.com/repos/starwars/lightsabers/releases' => Http::response(null, 500)]);

        $this->expectException(RequestException::class);

        app(GitHub::class)->releases('starwars/lightsabers');
    }

    /** @test */
    function requesting_user_sends_request_to_correct_url()
    {
        Http::fake(['https://api.github.com/users/lukeskywalker' => Http::response([])]);

        app(GitHub::class)->user('lukeskywalker');

        Http::assertSent(fn($request) => $request->url() === 'https://api.github.com/users/lukeskywalker');
    }

    /** @test */
    function requesting_user_throws_exception_when_request_has_error()
    {
        Http::fake(['https://api.github.com/users/lukeskywalker' => Http::response(null, 500)]);

        $this->expectException(RequestException::class);

        app(GitHub::class)->user('lukeskywalker');
    }
}
