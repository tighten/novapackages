<?php

namespace Tests\Unit;

use App\BaseRepo;
use App\NpmRepo;
use Facades\App\Repo;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NpmRepoTest extends TestCase
{
    /** @test */
    function it_returns_proper_readme_format()
    {
        Http::fake(['https://registry.npmjs.org/lodash/' => Http::response()]);

        $repo = NpmRepo::make('https://www.npmjs.com/package/lodash');

        $this->assertEquals(BaseRepo::README_FORMAT, $repo->readmeFormat());
    }

    /** @test */
    function it_returns_latest_release_if_set()
    {
        Http::fake([
            'https://registry.npmjs.org/lodash/' => Http::response(
                $this->fakeResponse('npm.repo-with-github-vcs.json')
            ),
            'https://api.github.com/repos/lodash/lodash/releases' =>
                Http::response($this->fakeResponse('github.repo-releases.json')),
        ]);

        $repo = Repo::fromUrl('https://www.npmjs.com/package/lodash');

        $this->assertEquals('v1.0.0', $repo->latestReleaseVersion());
    }

    /** @test */
    function it_returns_master_if_latest_release_is_not_set()
    {

        Http::fake([
            'https://registry.npmjs.org/lodash/' => Http::response(),
            'https://api.github.com/repos/lodash/lodash/releases' =>
                Http::response($this->fakeResponse('github.repo-releases.json')),
        ]);

        $repo = Repo::fromUrl('https://www.npmjs.com/package/lodash');

        $this->assertEquals('master', $repo->latestReleaseVersion());
    }
}
