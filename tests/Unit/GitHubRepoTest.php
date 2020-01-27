<?php

namespace Tests\Unit;

use App\GitHubRepo;
use App\Http\Remotes\GitHub;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class GitHubRepoTest extends TestCase
{
    /** @test */
    public function it_gets_the_latest_release_version_for_tagged_releases()
    {
        $this->mockGitHubWith([
            'releases' => collect([
                [
                    'name' => 'Release',
                    'tag_name' => 'v1.0',
                ],
            ]),
        ]);

        $repo = GitHubRepo::make('https://github.com/starwars/lightsabers');

        $this->assertEquals('v1.0', $repo->latestReleaseVersion());
    }

    /** @test */
    public function it_falls_back_to_master_when_there_are_no_releases()
    {
        $this->mockGitHubWith([
            'releases' => collect(),
        ]);

        $repo = GitHubRepo::make('https://github.com/starwars/x-wings');

        $this->assertEquals('master', $repo->latestReleaseVersion());
    }

    protected function mockGitHubWith($expectations)
    {
        $github = Mockery::mock(GitHub::class, $expectations);
        $github->shouldReceive('api')->andReturn($github);
        $github->shouldReceive('validateUrl')->andReturn(true);
        app()->instance(GitHub::class, $github);
    }
}
