<?php

namespace Tests\Feature;

use App\BaseRepo;
use App\BitBucketRepo;
use App\Exceptions\BitBucketException;
use App\Exceptions\GitLabException;
use App\GitHubRepo;
use App\GitLabRepo;
use App\Http\Requests\PackageFormRequest;
use App\NpmRepo;
use App\Package;
use Facades\App\Repo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Mockery;
use Tests\TestCase;

class RepoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake([
            'https://packagist.org/packages/tightenco/nova-stripe.json' =>
                Http::response([
                    'package' => ['repository' => 'https://github.com/tighten/nova-stripe'],
                ]),
            'https://api.github.com/repos/tighten/nova-stripe/readme' =>
                Http::response($this->fakeResponse('github.repo-readme.html')),
            'https://api.github.com/repos/tighten/nova-stripe/releases' =>
                Http::response($this->fakeResponse('github.repo-releases.json')),
        ]);
    }

    /** @test */
    public function can_get_a_repo_from_a_package_composer_name()
    {
        $composerName = 'tightenco/nova-stripe';
        $package = Package::factory()->make([
            'composer_name' => $composerName,
        ]);

        $repo = Repo::fromPackageModel($package);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitHubRepo::class, $repo->repo());
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/tighten/nova-stripe', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_get_a_repo_from_a_the_package_url_if_the_composer_name_is_not_valid()
    {
        $packageUrl = 'https://github.com/tighten/nova-stripe';

        Http::fake([
            'https://packagist.org/packages/invalid-namespace/invalid-name.json' => Http::response([
                'status' => 'error',
                'message' => 'Package not found',
            ]),
        ]);

        $package = Package::factory()->make([
            'composer_name' => 'invalid-namespace/invalid-name',
            'url' => $packageUrl,
        ]);

        $repo = Repo::fromPackageModel($package);

        $this->assertInstanceOf(GitHubRepo::class, $repo);
        $this->assertEquals('github', $repo->source());
        $this->assertEquals($packageUrl, $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_get_a_repo_from_a_packagist_composer_name()
    {
        $url = 'https://packagist.org/packages/tightenco/nova-stripe';
        $mock = Mockery::mock(PackageFormRequest::class);
        $mock->shouldReceive('input')->with('url')->andReturn($url);
        $mock->shouldReceive('getComposerName')->andReturn('tightenco/nova-stripe');

        $repo = Repo::fromRequest($mock);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitHubRepo::class, $repo->repo());
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/tighten/nova-stripe', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_get_a_repo_from_a_the_request_url_if_the_composer_name_is_not_valid()
    {
        Http::fake([
            'https://packagist.org/packages/invalid-namespace/invalid-name.json' => Http::response([
                'status' => 'error',
                'message' => 'Package not found',
            ]),
        ]);

        $url = 'https://github.com/tighten/nova-stripe';
        $mock = Mockery::mock(PackageFormRequest::class);
        $mock->shouldReceive('input')->with('url')->andReturn($url);
        $mock->shouldReceive('getComposerName')->andReturn('invalid-namespace/invalid-name');

        $repo = Repo::fromRequest($mock);

        $this->assertInstanceOf(GitHubRepo::class, $repo);
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/tighten/nova-stripe', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_data_from_a_github_repo()
    {
        $githubUrl = 'https://github.com/tighten/nova-stripe';

        $repo = Repo::fromUrl($githubUrl);

        $this->assertInstanceOf(GitHubRepo::class, $repo);
        $this->assertEquals('github', $repo->source());
        $this->assertEquals($githubUrl, $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function github_readme_is_returned_as_null_if_one_is_not_present()
    {
        $repositoryPath = 'ctroms/no-readme-test';

        $url = "https://github.com/{$repositoryPath}";

        Http::fake([
            "https://api.github.com/repos/{$repositoryPath}/readme" =>
                Http::response($this->fakeResponse('github.repo-readme-404.json'), 404),
        ]);

        $repo = Repo::fromUrl($url);

        $this->assertNull($repo->readme());
    }

    /** @test */
    public function can_fetch_data_from_a_bitbucket_repo()
    {
        Http::fake([
            'https://api.bitbucket.org/2.0/repositories/tightenco/novapackages-test/refs' => Http::response(),
            'https://api.bitbucket.org/2.0/repositories/tightenco/novapackages-test/src/master/README.md' =>
                Http::response(['# novapackages-test']),
        ]);

        $bitBucketUrl = 'https://bitbucket.org/tightenco/novapackages-test';

        $repo = Repo::fromUrl($bitBucketUrl);

        $this->assertInstanceOf(BitBucketRepo::class, $repo);
        $this->assertEquals('bitbucket', $repo->source());
        $this->assertEquals($bitBucketUrl, $repo->url());
        $this->assertTrue(str_contains($repo->readme(), '# novapackages-test'));
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function an_exception_is_thrown_if_bitbucket_response_has_errors_that_are_not_file_not_found_errors()
    {
        Http::fake([
            'https://api.bitbucket.org/2.0/repositories/invalid-user/invalid-repo/refs' => Http::response([
                'type' => 'error',
            ]),
        ]);

        $bitBucketUrl = 'https://bitbucket.org/invalid-user/invalid-repo';

        $this->expectException(BitBucketException::class);

        Repo::fromUrl($bitBucketUrl)->readme();
    }

    /** @test */
    public function can_fetch_data_from_a_gitlab_repo()
    {
        $url = 'https://gitlab.com/alphayax/rancher-api';

        Http::fake([
            $url => Http::response(),
            'https://gitlab.com/api/v4/projects/alphayax%2Francher-api/repository/tags' => Http::response($this->fakeResponse('gitlab.repo-tags.json')),
            'https://gitlab.com/api/v4/projects/alphayax%2Francher-api/repository/files/README%2Emd?ref=2.0.4' =>
                Http::response($this->fakeResponse('gitlab.repo-readme.json')),
        ]);

        $repo = Repo::fromUrl($url);

        $this->assertInstanceOf(GitLabRepo::class, $repo);
        $this->assertEquals('gitlab', $repo->source());
        $this->assertEquals($url, $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertTrue(str_contains($repo->readme(), '# Rancher-API'));
        $this->assertEquals('2.0.4', $repo->latestReleaseVersion());
    }

    /** @test */
    public function an_exception_is_thrown_if_gitlab_response_has_errors_that_are_not_file_not_found_errors()
    {
        $gitlabUrl = 'https://gitlab.com/invalid-user/invalid-repo';

        Http::fake([
            $gitlabUrl => Http::response([]),
            'https://gitlab.com/api/v4/projects/invalid-user%2Finvalid-repo/repository/tags' => Http::response([
                'message' => '404 Project Not Found',
            ]),
        ]);

        $this->expectException(GitLabException::class);

        Repo::fromUrl($gitlabUrl)->readme();
    }

    /** @test */
    public function gitlab_readme_is_returned_as_null_if_one_is_not_present()
    {
        $url = 'https://gitlab.com/jedi/how-to-join-the-sith';

        Http::fake([
            $url => Http::response(),
            'https://gitlab.com/api/v4/projects/jedi%2Fhow-to-join-the-sith/repository/tags' => Http::response([]),
            'https://gitlab.com/api/v4/projects/jedi%2Fhow-to-join-the-sith/repository/files/README%2Emd?ref=master' =>
                Http::response(['message' => '404 Commit Not Found']),

        ]);

        $this->expectException(GitLabException::class);

        $repo = Repo::fromUrl($url);

        $this->assertNull($repo->readme());
    }

    /** @test */
    public function can_fetch_the_github_data_from_an_npm_package_with_a_github_vcs()
    {
        // lodash is an example of a package that has been
        // published to npm with a GitHub VCS.
        $npmUrl = 'https://www.npmjs.com/package/lodash';

        Http::fake([
            'https://registry.npmjs.org/lodash/' => Http::response(
                $this->fakeResponse('npm.repo-with-github-vcs.json')
            ),
        ]);

        $repo = Repo::fromUrl($npmUrl);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitHubRepo::class, $repo->repo());
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/lodash/lodash.git', $repo->url());
    }

    /** @test */
    public function can_fetch_the_npm_data_if_the_package_doesnt_have_a_vcs()
    {
        // vue-form-state is an example of a package that has been
        // published to npm without a VCS.
        $npmUrl = 'https://www.npmjs.com/package/vue-form-state';

        Http::fake([
            'https://registry.npmjs.org/vue-form-state/' =>
                Http::response($this->fakeResponse('npm.package-without-vcs.json')),
        ]);

        $repo = Repo::fromUrl($npmUrl);

        $this->assertInstanceOf(NpmRepo::class, $repo);
        $this->assertEquals('npm', $repo->source());
        $this->assertEquals('https://www.npmjs.com/package/vue-form-state', $repo->url());
        $this->assertNotNull($repo->readme());
    }

    /** @test */
    public function can_fetch_the_npm_data_if_the_package_has_a_vcs_we_do_not_integrate_with()
    {
        $this->markTestIncomplete('Need to find/create a package that matches this use case');
    }

    /** @test */
    public function return_default_data_for_an_npm_package_without_a_vcs_or_a_readme()
    {
        $this->markTestIncomplete('Need to find/create a package that matches this use case');
    }

    /** @test */
    public function can_fetch_the_default_data_for_a_vcs_we_dont_integrate_with()
    {
        $npmUrl = 'https://www.example.com/package/a-new-package';

        $repo = Repo::fromUrl($npmUrl);

        $this->assertInstanceOf(BaseRepo::class, $repo);
        $this->assertEquals('www.example.com', $repo->source());
        $this->assertEquals('https://www.example.com/package/a-new-package', $repo->url());
        $this->assertNull($repo->readme());
        $this->assertNull($repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_the_default_data_for_a_url_without_the_trailing_slash()
    {
        $npmUrl = 'https://www.example.com';

        $repo = Repo::fromUrl($npmUrl);

        $this->assertInstanceOf(BaseRepo::class, $repo);
        $this->assertEquals('www.example.com', $repo->source());
        $this->assertEquals('https://www.example.com', $repo->url());
        $this->assertNull($repo->readme());
        $this->assertNull($repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_the_github_data_from_a_packagist_package_with_a_github_vcs()
    {
        $url = 'https://packagist.org/packages/tightenco/nova-stripe';

        Http::fake([
            "{$url}.json" =>
                Http::response($this->fakeResponse('packagist.repo-with-github-vcs.json')),
        ]);

        $repo = Repo::fromUrl($url);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitHubRepo::class, $repo->repo());
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/tighten/nova-stripe', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertEquals('v1.0.0', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_the_bitbucket_data_from_a_packagist_package_with_a_bitbucket_vcs()
    {
        // This package is an example of a package that is
        // backed by a Bitbucket repository.
        $packagistUrl = 'https://packagist.org/packages/adnanchowdhury/uploadcare-image';

        Http::fake([
            "{$packagistUrl}.json" =>
                Http::response($this->fakeResponse('packagist.repo-with-bitbucket-vcs.json')),
            // This request is currently run twice in this test case
            // for $repo->readme() and $repo->latestReleaseVersion().
            // Caching should be introduced to avoid this.
            'https://api.bitbucket.org/2.0/repositories/adnanchowdhury/nova-uploadcare-imagefield/refs' =>
                Http::sequence()
                    ->push($this->fakeResponse('bitbucket.repo-refs.json'))
                    ->push($this->fakeResponse('bitbucket.repo-refs.json')),
            'https://api.bitbucket.org/2.0/repositories/adnanchowdhury/nova-uploadcare-imagefield/src/0.0.3/README.md' =>
                Http::response($this->fakeResponse('bitbucket.repo-readme.md')),
        ]);

        $repo = Repo::fromUrl($packagistUrl);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(BitBucketRepo::class, $repo->repo());
        $this->assertEquals('bitbucket', $repo->source());
        $this->assertEquals('https://bitbucket.org/adnanchowdhury/nova-uploadcare-imagefield.git', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertEquals('0.0.3', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_the_gitlab_data_from_a_packagist_package_with_a_gitlab_vcs()
    {
        $packagistUrl = 'https://packagist.org/packages/alphayax/rancher-api';
        $repoUrl = 'https://gitlab.com/alphayax/rancher-api';

        Http::fake([
            "{$packagistUrl}.json" => Http::response($this->fakeResponse('packagist.repo-with-gitlab-vcs.json')),
            $repoUrl => Http::response(),
            'https://gitlab.com/api/v4/projects/alphayax%2Francher-api/repository/tags' => Http::response($this->fakeResponse('gitlab.repo-tags.json')),
            'https://gitlab.com/api/v4/projects/alphayax%2Francher-api/repository/files/README%2Emd?ref=2.0.4' =>
                Http::response($this->fakeResponse('gitlab.repo-readme.json')),
        ]);

        $repo = Repo::fromUrl($packagistUrl);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitLabRepo::class, $repo->repo());
        $this->assertEquals('gitlab', $repo->source());
        $this->assertEquals($repoUrl, $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertTrue(str_contains($repo->readme(), '# Rancher-API'));
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertEquals('2.0.4', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_the_default_data_from_a_packagist_package_with_a_vcs_we_do_not_integrate_with()
    {
        $this->markTestIncomplete('Need to find/create a package that matches this use case');
    }
}
