<?php

use App\BaseRepo;
use App\GitLabRepo;
use App\Http\Remotes\GitLab;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

it('sets the url for a changed username to the new repository location', function () {
    Http::fake([
        'https://gitlab.com/senator-palpatine/masterplan' => Http::response(null, 301, [
            'Location' => ['https://gitlab.com/emperor-palpatine/masterplan'],
        ]),
    ]);

    $repo = GitLabRepo::make('https://gitlab.com/senator-palpatine/masterplan');

    expect($repo->url())->toEqual('https://gitlab.com/emperor-palpatine/masterplan');
});

it('gets the latest release version for tagged releases', function () {
    Http::fake(['https://gitlab.com/starwars/lightsabers' => Http::response()]);

    mockGitLabWith([
        'fetchData' => collect([
            [
                'name' => 'v1.0',
            ],
        ]),
    ]);

    $repo = GitLabRepo::make('https://gitlab.com/starwars/lightsabers');

    expect($repo->latestReleaseVersion())->toEqual('v1.0');
});

it('falls back to master when there are no releases', function () {
    Http::fake(['https://gitlab.com/starwars/x-wings' => Http::response()]);

    mockGitLabWith([
        'fetchData' => collect(),
    ]);

    $repo = GitLabRepo::make('https://gitlab.com/starwars/x-wings');

    expect($repo->latestReleaseVersion())->toEqual('master');
});

it('returns proper readme format', function () {
    Http::fake(['https://gitlab.com/starwars/lightsabers' => Http::response()]);

    $repo = GitLabRepo::make('https://gitlab.com/starwars/lightsabers');

    expect($repo->readmeFormat())->toEqual(BaseRepo::README_FORMAT);
});

// Helpers
function mockGitLabWith($expectations)
{
    $gitlab = Mockery::mock(GitLab::class, $expectations);
    $gitlab->shouldReceive('validateUrl')->andReturn(true);
    app()->bind(GitLab::class, function () use ($gitlab) {
        return $gitlab;
    });
}
