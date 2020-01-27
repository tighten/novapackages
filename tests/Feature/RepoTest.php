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
use App\NullRepo;
use App\Package;
use Facades\App\Repo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

/** @group integration */
class RepoTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_a_repo_from_a_package_composer_name()
    {
        $composerName = 'tightenco/nova-stripe';
        $package = factory(Package::class)->make([
            'composer_name' => $composerName,
        ]);

        $repo = Repo::fromPackageModel($package);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitHubRepo::class, $repo->repo());
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/tightenco/nova-stripe', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_get_a_repo_from_a_the_package_url_if_the_composer_name_is_not_valid()
    {
        $url = 'https://github.com/tightenco/nova-stripe';
        $package = factory(Package::class)->make([
            'composer_name' => 'invalid-namespace/invalid-name',
            'url' => $url,
        ]);

        $repo = Repo::fromPackageModel($package);

        $this->assertInstanceOf(GitHubRepo::class, $repo);
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/tightenco/nova-stripe', $repo->url());
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
        $mock->shouldReceive('getComposerName')->andReturn('tigtenco/nova-stripe');

        $repo = Repo::fromRequest($mock);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitHubRepo::class, $repo->repo());
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/tightenco/nova-stripe', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_get_a_repo_from_a_the_request_url_if_the_composer_name_is_not_valid()
    {
        $url = 'https://github.com/tightenco/nova-stripe';
        $mock = Mockery::mock(PackageFormRequest::class);
        $mock->shouldReceive('input')->with('url')->andReturn($url);
        $mock->shouldReceive('getComposerName')->andReturn('invalid-namespace/invalid-name');

        $repo = Repo::fromRequest($mock);

        $this->assertInstanceOf(GitHubRepo::class, $repo);
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/tightenco/nova-stripe', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_data_from_a_github_repo()
    {
        $githubUrl = 'https://github.com/tightenco/nova-stripe';

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
        $url = 'https://github.com/ctroms/no-readme-test';

        $repo = Repo::fromUrl($url);

        $this->assertEquals('github', $repo->source());
        $this->assertEquals($url, $repo->url());
        $this->assertNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_data_from_a_bitbucket_repo()
    {
        $bitBucketUrl = 'https://bitbucket.org/tightenco/novapackages-test';

        $repo = Repo::fromUrl($bitBucketUrl);

        $this->assertInstanceOf(BitBucketRepo::class, $repo);
        $this->assertEquals('bitbucket', $repo->source());
        $this->assertEquals($bitBucketUrl, $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertTrue(strpos($repo->readme(), '# novapackages-test') !== false);
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function an_exception_is_thrown_if_bitbucket_response_has_errors_that_are_not_file_not_found_errors()
    {
        $bitBucketUrl = 'https://bitbucket.org/invalid-user/invalid-repo';

        $this->expectException(BitBucketException::class);

        Repo::fromUrl($bitBucketUrl)->readme();
    }

    /** @test */
    public function can_fetch_data_from_a_gitlab_repo()
    {
        $url = 'https://gitlab.com/ctroms/test-project';

        $repo = Repo::fromUrl($url);

        $this->assertInstanceOf(GitLabRepo::class, $repo);
        $this->assertEquals('gitlab', $repo->source());
        $this->assertEquals($url, $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertTrue(strpos($repo->readme(), '# Test Project') !== false);
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function an_exception_is_thrown_if_gitlab_response_has_errors_that_are_not_file_not_found_errors()
    {
        $bitBucketUrl = 'https://gitlab.com/invalid-user/invalid-repo';

        $this->expectException(GitLabException::class);

        Repo::fromUrl($bitBucketUrl)->readme();
    }

    /** @test */
    public function gitlab_readme_is_returned_as_null_if_one_is_not_present()
    {
        $this->markTestIncomplete('Need to find/create a package that matches this use case');
    }

    /** @test */
    public function can_fetch_the_github_data_from_an_npm_package_with_a_github_vcs()
    {
        // This package was selected because this test needs a package that has been published
        // on npm with a github VCS. At the moment, Tighten does not own an npm packages that
        // meets this criteria.
        $npmUrl = 'https://www.npmjs.com/package/lodash';

        $repo = Repo::fromUrl($npmUrl);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitHubRepo::class, $repo->repo());
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/lodash/lodash.git', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_the_npm_data_if_the_package_doesnt_have_a_vcs()
    {
        $npmUrl = 'https://www.npmjs.com/package/vue-form-state';

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

        $repo = Repo::fromUrl($url);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitHubRepo::class, $repo->repo());
        $this->assertEquals('github', $repo->source());
        $this->assertEquals('https://github.com/tightenco/nova-stripe', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_the_bitbucket_data_from_a_packagist_package_with_a_bitbucket_vcs()
    {
        // This package was selected because this test needs a package that has been published
        // on packagist with a bitbucket VCS. At the moment, Tighten does not own a package
        // that meet this criteria.
        $bitBucketUrl = 'https://packagist.org/packages/adnanchowdhury/uploadcare-image';

        $repo = Repo::fromUrl($bitBucketUrl);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(BitBucketRepo::class, $repo->repo());
        $this->assertEquals('bitbucket', $repo->source());
        $this->assertEquals('https://bitbucket.org/adnanchowdhury/nova-uploadcare-imagefield.git', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_the_gitlab_data_from_a_packagist_package_with_a_gitlab_vcs()
    {
        // This package was selected because this test needs a package that has been published
        // on packagist with a gitlab VCS. At the moment, Tighten does not own any packages
        // that meet this criteria.
        $packagistUrl = 'https://packagist.org/packages/alphayax/rancher-api';

        $repo = Repo::fromUrl($packagistUrl);

        $this->assertNotNull($repo->repo());
        $this->assertInstanceOf(GitLabRepo::class, $repo->repo());
        $this->assertEquals('gitlab', $repo->source());
        $this->assertEquals('https://gitlab.com/alphayax/rancher-api', $repo->url());
        $this->assertNotNull($repo->readme());
        $this->assertTrue(strpos($repo->readme(), '# Rancher-API') !== false);
        $this->assertNotNull($repo->latestReleaseVersion());
        $this->assertNotEquals('master', $repo->latestReleaseVersion());
    }

    /** @test */
    public function can_fetch_the_default_data_from_a_packagist_package_with_a_vcs_we_do_not_integrate_with()
    {
        $this->markTestIncomplete('Need to find/create a package that matches this use case');
    }
}
