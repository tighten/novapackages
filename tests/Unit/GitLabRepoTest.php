<?php

namespace Tests\Unit;

use App\GitLabRepo;
use App\Http\Remotes\GitLab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Tests\TestCase;

class GitLabRepoTest extends TestCase
{
    /** @test */
    public function it_gets_the_latest_release_version_for_tagged_releases()
    {
        $this->mockGitLabWith([
            'fetchData' => collect([
                [
                    'name' => 'v1.0',
                ],
            ]),
        ]);

        $repo = GitLabRepo::make('https://gitlab.com/starwars/lightsabers');

        $this->assertEquals('v1.0', $repo->latestReleaseVersion());
    }

    /** @test */
    public function it_falls_back_to_master_when_there_are_no_releases()
    {
        $this->mockGitLabWith([
            'fetchData' => collect(),
        ]);

        $repo = GitLabRepo::make('https://gitlab.com/starwars/x-wings');

        $this->assertEquals('master', $repo->latestReleaseVersion());
    }

    protected function mockGitLabWith($expectations)
    {
        $gitlab = Mockery::mock(GitLab::class, $expectations);
        $gitlab->shouldReceive('validateUrl')->andReturn(true);
        app()->bind(GitLab::class, function () use ($gitlab) {
            return $gitlab;
        });
    }
}
