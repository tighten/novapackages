<?php

namespace Tests\Unit;

use App\Exceptions\GitHubException;
use App\GitHubRepo;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GitHubRepoTest extends TestCase
{
    /** @test */
    function requires_valid_url()
    {
        $this->expectException(GitHubException::class);

        GitHubRepo::make('https://notgithub.com/starwars/lightsabers');
    }

    /** @test */
    public function it_gets_the_latest_release_version_for_tagged_releases()
    {
        Http::fake([
            'https://api.github.com/repos/starwars/lightsabers/releases' => Http::response(collect([
                [
                    'name' => 'Release',
                    'tag_name' => 'v1.0',
                ],
            ])),
        ]);

        $repo = GitHubRepo::make('https://github.com/starwars/lightsabers');

        $this->assertEquals('v1.0', $repo->latestReleaseVersion());
    }

    /** @test */
    public function it_falls_back_to_master_when_there_are_no_releases()
    {
        Http::fake(['https://api.github.com/repos/starwars/lightsabers/releases' => Http::response([])]);

        $repo = GitHubRepo::make('https://github.com/starwars/lightsabers');

        $this->assertEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    function it_returns_proper_readme_format()
    {
        $repo = GitHubRepo::make('https://github.com/starwars/lightsabers');

        $this->assertEquals(GitHubRepo::README_FORMAT, $repo->readmeFormat());
    }
}
