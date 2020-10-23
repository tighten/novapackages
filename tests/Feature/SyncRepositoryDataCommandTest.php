<?php

namespace Tests\Feature;

use App\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SyncRepositoryDataCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function calling_the_command_without_an_argument_updates_all_packages_with_data_from_their_remote_repos()
    {
        $packageA = Package::factory()->create([
            'readme' => 'old readme A',
            'readme_source' => 'old source A',
            'repo_url' => 'http://example.com/my-repoA',
            'latest_version' => 'v1.2.3',
        ]);
        $repoAttributesA = [
            'url' => 'https://fake-github.com/fake-user/fake-repo',
            'source' => 'github',
            'readme' => '# Fake readme',
            'latest_version' => 'v2.3.4',
        ];

        $packageB = Package::factory()->create([
            'readme' => 'old readme B',
            'readme_source' => 'old source B',
            'repo_url' => 'http://example.com/my-repoB',
            'latest_version' => 'v3.4.5',
        ]);
        $repoAttributesB = [
            'url' => 'https://fake-github.com/fake-user/fake-repo',
            'source' => 'github',
            'readme' => '# Fake readme',
            'latest_version' => 'v4.5.6',
        ];

        $this->fakeRepoFromPackageModel([$repoAttributesA, $repoAttributesB]);

        $this->artisan('sync:repo');

        $this->assertCount(2, Package::all());
        $packageA->refresh();
        $this->assertEquals($repoAttributesA['url'], $packageA->repo_url);
        $this->assertEquals($repoAttributesA['source'], $packageA->readme_source);
        $this->assertEquals($repoAttributesA['readme'], $packageA->readme);
        $this->assertEquals($repoAttributesA['latest_version'], $packageA->latest_version);
        $packageB->refresh();
        $this->assertEquals($repoAttributesB['url'], $packageB->repo_url);
        $this->assertEquals($repoAttributesB['source'], $packageB->readme_source);
        $this->assertEquals($repoAttributesB['readme'], $packageB->readme);
        $this->assertEquals($repoAttributesB['latest_version'], $packageB->latest_version);
    }

    /** @test */
    public function calling_the_command_with_a_package_id_only_updates_that_package_with_data_from_its_remote_repo()
    {
        $packageA = Package::factory()->create([
            'readme' => 'old readme A',
            'readme_source' => 'old source A',
            'repo_url' => 'http://example.com/my-repoA',
            'latest_version' => 'v1.2.3',
        ]);
        $repoAttributesA = [
            'url' => 'https://fake-github.com/fake-user/fake-repo',
            'source' => 'github',
            'readme' => '# Fake readme',
            'latest_version' => 'v2.3.4',
        ];
        $packageBAttributes = [
            'readme' => 'old readme B',
            'readme_source' => 'old source B',
            'repo_url' => 'http://example.com/my-repoB',
            'latest_version' => 'v3.4.5',
        ];
        $packageB = Package::factory()->create($packageBAttributes);
        $repoAttributesB = [
            'url' => 'https://fake-github.com/fake-user/fake-repo',
            'source' => 'github',
            'readme' => '# Fake readme',
            'latest_version' => 'v4.5.6',
        ];

        $this->fakeRepoFromPackageModel([$repoAttributesA, $repoAttributesB]);

        $this->artisan('sync:repo', ['package' => $packageA->id]);

        $this->assertCount(2, Package::all());
        $packageA->refresh();
        $this->assertEquals($repoAttributesA['url'], $packageA->repo_url);
        $this->assertEquals($repoAttributesA['source'], $packageA->readme_source);
        $this->assertEquals($repoAttributesA['readme'], $packageA->readme);
        $this->assertEquals($repoAttributesA['latest_version'], $packageA->latest_version);
        $packageB->refresh();
        $this->assertEquals($packageBAttributes['readme'], $packageB->readme);
        $this->assertEquals($packageBAttributes['readme_source'], $packageB->readme_source);
        $this->assertEquals($packageBAttributes['repo_url'], $packageB->repo_url);
        $this->assertEquals($packageBAttributes['latest_version'], $packageB->latest_version);
    }

    /** @test */
    public function local_data_is_not_updated_if_there_are_no_changes_in_the_remote_repo()
    {
        $updatedAt = Carbon::now()->subHour();
        $package = Package::factory()->create([
            'repo_url' => 'https://fake-github.com/fake-user/fake-repo',
            'readme_source' => 'github',
            'readme' => '# Fake readme',
            'readme_format' => 'md',
            'latest_version' => 'v2.3.4',
            'updated_at' => $updatedAt,
        ]);

        $this->fakeRepoFromPackageModel([
            'url' => 'https://fake-github.com/fake-user/fake-repo',
            'source' => 'github',
            'readme' => '# Fake readme',
            'readme_format' => 'md',
            'latest_version' => 'v2.3.4',
        ]);

        $this->artisan('sync:repo', ['package' => $package->id]);
        $package->refresh();
        $this->assertCount(1, Package::all());
        $this->assertEquals($updatedAt->toDateTimeString(), $package->updated_at->toDateTimeString());
    }
}
