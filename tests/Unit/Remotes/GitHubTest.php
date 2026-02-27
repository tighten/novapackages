<?php

use App\Exceptions\GitHubException;
use App\Http\Remotes\GitHub;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

uses(Tests\TestCase::class);

test('validates github urls', function () {
    expect(GitHub::validateUrl('http://github.com/starwars/lightsabers'))->toBeTrue();
    expect(GitHub::validateUrl('https://github.com/starwars/lightsabers'))->toBeTrue();

    expect(GitHub::validateUrl('https://subdomain.github.com/starwars/lightsabers'))->toBeFalse();
    expect(GitHub::validateUrl('https://notgithub.com/starwars/lightsabers'))->toBeFalse();
});

test('requesting package idea issues sends request to correct url', function () {
    Http::fake([
        'https://api.github.com/search/issues*' => Http::response($this->fakeResponse('github.search-issues-in-repo.json')),
    ]);

    app(GitHub::class)->packageIdeaIssues();

    Http::assertSent(function ($request) {
        $url = $request->url();

        return str_contains($url, 'https://api.github.com/search/issues')
            && str_contains($url, urlencode('repo:tighten/nova-package-development'));
    });
});

test('requesting package idea issues throws exception when request has error', function () {
    Http::fake(['https://api.github.com/search/issues*' => Http::response(null, 500)]);

    $this->expectException(RequestException::class);

    app(GitHub::class)->packageIdeaIssues();
});

test('requesting readme requires owner and repository', function () {
    $this->expectException(GitHubException::class);

    app(GitHub::class)->readme('invalid');
});

test('requesting readme sends request to correct url', function () {
    Http::fake(['https://api.github.com/repos/starwars/lightsabers/readme' => Http::response()]);

    app(GitHub::class)->readme('starwars/lightsabers');

    Http::assertSent(
        fn ($request) => $request->url() === 'https://api.github.com/repos/starwars/lightsabers/readme'
    );
});

test('requesting readme returns null when readme does not exist', function () {
    Http::fake(['https://api.github.com/repos/starwars/lightsabers/readme' => Http::response(null, 404)]);

    $response = app(GitHub::class)->readme('starwars/lightsabers');

    expect($response)->toBeNull();
});

test('requesting readme throws exception when request has error', function () {
    Http::fake(['https://api.github.com/repos/starwars/lightsabers/readme' => Http::response(null, 500)]);

    $this->expectException(RequestException::class);

    app(GitHub::class)->readme('starwars/lightsabers');
});

test('requesting releases requires owner and repository', function () {
    $this->expectException(GitHubException::class);

    app(GitHub::class)->releases('invalid');
});

test('requesting releases sends request to correct url', function () {
    Http::fake(['https://api.github.com/repos/starwars/lightsabers/releases' => Http::response([])]);

    app(GitHub::class)->releases('starwars/lightsabers');

    Http::assertSent(
        fn ($request) => $request->url() === 'https://api.github.com/repos/starwars/lightsabers/releases'
    );
});

test('requesting releases throws exception when request has error', function () {
    Http::fake(['https://api.github.com/repos/starwars/lightsabers/releases' => Http::response(null, 500)]);

    $this->expectException(RequestException::class);

    app(GitHub::class)->releases('starwars/lightsabers');
});

test('requesting user sends request to correct url', function () {
    Http::fake(['https://api.github.com/users/lukeskywalker' => Http::response([])]);

    app(GitHub::class)->user('lukeskywalker');

    Http::assertSent(fn ($request) => $request->url() === 'https://api.github.com/users/lukeskywalker');
});

test('requesting user throws exception when request has error', function () {
    Http::fake(['https://api.github.com/users/lukeskywalker' => Http::response(null, 500)]);

    $this->expectException(RequestException::class);

    app(GitHub::class)->user('lukeskywalker');
});
