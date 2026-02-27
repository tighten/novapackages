<?php

use App\BaseRepo;
use App\BitBucketRepo;
use App\Http\Remotes\BitBucket;
use Tests\TestCase;

uses(Tests\TestCase::class);

it('gets the latest release version for tagged releases', function () {
    mockBitBucketWith([
        'fetchData' => [
            'values' => [
                [
                    'name' => 'v1.0',
                ],
            ],
        ],
    ]);

    $repo = BitBucketRepo::make('https://bitbucket.org/starwars/lightsabers');

    expect($repo->latestReleaseVersion())->toEqual('v1.0');
});

it('falls back to master when there are no releases', function () {
    mockBitBucketWith([
        'fetchData' => ['values' => []],
    ]);

    $repo = BitBucketRepo::make('https://bitbucket.org/starwars/x-wings');

    expect($repo->latestReleaseVersion())->toEqual('master');
});

it('returns proper readme format', function () {
    $repo = BitBucketRepo::make('https://bitbucket.org/starwars/lightsabers');

    expect($repo->readmeFormat())->toEqual(BaseRepo::README_FORMAT);
});

// Helpers
function mockBitBucketWith($expectations)
{
    $bitbucket = Mockery::mock(BitBucket::class, $expectations);
    $bitbucket->shouldReceive('validateUrl')->andReturn(true);
    app()->bind(BitBucket::class, function () use ($bitbucket) {
        return $bitbucket;
    });
}
