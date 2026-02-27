<?php

use App\Exceptions\GitHubException;
use App\GitHubRepo;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

test('requires valid url', function () {
    $this->expectException(GitHubException::class);

    GitHubRepo::make('https://notgithub.com/starwars/lightsabers');
});

it('gets the latest release version for tagged releases', function () {
    Http::fake([
        'https://api.github.com/repos/starwars/lightsabers/releases' => Http::response(collect([
            [
                'name' => 'Release',
                'tag_name' => 'v1.0',
            ],
        ])),
    ]);

    $repo = GitHubRepo::make('https://github.com/starwars/lightsabers');

    expect($repo->latestReleaseVersion())->toEqual('v1.0');
});

it('falls back to master when there are no releases', function () {
    Http::fake(['https://api.github.com/repos/starwars/lightsabers/releases' => Http::response([])]);

    $repo = GitHubRepo::make('https://github.com/starwars/lightsabers');

    expect($repo->latestReleaseVersion())->toEqual('master');
});

it('returns proper readme format', function () {
    $repo = GitHubRepo::make('https://github.com/starwars/lightsabers');

    expect($repo->readmeFormat())->toEqual(GitHubRepo::README_FORMAT);
});
