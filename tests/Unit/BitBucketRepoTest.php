<?php

namespace Tests\Unit;

use App\BitBucketRepo;
use App\Http\Remotes\BitBucket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class BitBucketRepoTest extends TestCase
{
    /** @test */
    public function it_gets_the_latest_release_version_for_tagged_releases()
    {
        $this->mockBitBucketWith([
            'fetchData' => [
                'values' => [
                    [
                        'name' => 'v1.0',
                    ],
                ],
            ],
        ]);

        $repo = BitBucketRepo::make('https://bitbucket.org/starwars/lightsabers');

        $this->assertEquals('v1.0', $repo->latestReleaseVersion());
    }

    /** @test */
    public function it_falls_back_to_master_when_there_are_no_releases()
    {
        $this->mockBitBucketWith([
            'fetchData' => ['values' => []],
        ]);

        $repo = BitBucketRepo::make('https://bitbucket.org/starwars/x-wings');

        $this->assertEquals('master', $repo->latestReleaseVersion());
    }

    protected function mockBitBucketWith($expectations)
    {
        $bitbucket = Mockery::mock(BitBucket::class, $expectations);
        $bitbucket->shouldReceive('validateUrl')->andReturn(true);
        app()->bind(BitBucket::class, function () use ($bitbucket) {
            return $bitbucket;
        });
    }
}
