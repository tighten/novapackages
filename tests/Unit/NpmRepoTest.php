<?php

use App\BaseRepo;
use App\NpmRepo;
use Facades\App\Repo;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);

it('returns proper readme format', function () {
    Http::fake(['https://registry.npmjs.org/lodash/' => Http::response()]);

    $repo = NpmRepo::make('https://www.npmjs.com/package/lodash');

    expect($repo->readmeFormat())->toEqual(BaseRepo::README_FORMAT);
});

it('returns latest release if set', function () {
    Http::fake([
        'https://registry.npmjs.org/lodash/' => Http::response(
            $this->fakeResponse('npm.repo-with-github-vcs.json')
        ),
        'https://api.github.com/repos/lodash/lodash/releases' => Http::response($this->fakeResponse('github.repo-releases.json')),
    ]);

    $repo = Repo::fromUrl('https://www.npmjs.com/package/lodash');

    expect($repo->latestReleaseVersion())->toEqual('v1.0.0');
});

it('returns master if latest release is not set', function () {
    Http::fake([
        'https://registry.npmjs.org/lodash/' => Http::response(),
        'https://api.github.com/repos/lodash/lodash/releases' => Http::response($this->fakeResponse('github.repo-releases.json')),
    ]);

    $repo = Repo::fromUrl('https://www.npmjs.com/package/lodash');

    expect($repo->latestReleaseVersion())->toEqual('master');
});
