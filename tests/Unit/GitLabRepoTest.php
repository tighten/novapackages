<?php

namespace Tests\Unit;

use App\BaseRepo;
use App\GitLabRepo;
use App\Http\Remotes\GitLab;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class GitLabRepoTest extends TestCase
{
    /** @test */
    function it_sets_the_url_for_a_changed_username_to_the_new_repository_location(): void
    {
        Http::fake([
            'https://gitlab.com/senator-palpatine/masterplan' => Http::response(null, 301, [
                'Location' => ['https://gitlab.com/emperor-palpatine/masterplan'],
            ]),
        ]);

        $repo = GitLabRepo::make('https://gitlab.com/senator-palpatine/masterplan');

        $this->assertEquals('https://gitlab.com/emperor-palpatine/masterplan', $repo->url());
    }

    /** @test */
    public function it_gets_the_latest_release_version_for_tagged_releases(): void
    {
        Http::fake(['https://gitlab.com/starwars/lightsabers' => Http::response()]);

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
    public function it_falls_back_to_master_when_there_are_no_releases(): void
    {
        Http::fake(['https://gitlab.com/starwars/x-wings' => Http::response()]);

        $this->mockGitLabWith([
            'fetchData' => collect(),
        ]);

        $repo = GitLabRepo::make('https://gitlab.com/starwars/x-wings');

        $this->assertEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    function it_returns_proper_readme_format(): void
    {
        Http::fake(['https://gitlab.com/starwars/lightsabers' => Http::response()]);

        $repo = GitLabRepo::make('https://gitlab.com/starwars/lightsabers');

        $this->assertEquals(BaseRepo::README_FORMAT, $repo->readmeFormat());
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
