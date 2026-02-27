<?php

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;


test('calling the command without an argument updates all packages with data from their remote repos', function () {
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

    expect(Package::all())->toHaveCount(2);
    $packageA->refresh();
    expect($packageA->repo_url)->toEqual($repoAttributesA['url']);
    expect($packageA->readme_source)->toEqual($repoAttributesA['source']);
    expect($packageA->readme)->toEqual($repoAttributesA['readme']);
    expect($packageA->latest_version)->toEqual($repoAttributesA['latest_version']);
    $packageB->refresh();
    expect($packageB->repo_url)->toEqual($repoAttributesB['url']);
    expect($packageB->readme_source)->toEqual($repoAttributesB['source']);
    expect($packageB->readme)->toEqual($repoAttributesB['readme']);
    expect($packageB->latest_version)->toEqual($repoAttributesB['latest_version']);
});

test('calling the command with a package id only updates that package with data from its remote repo', function () {
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

    expect(Package::all())->toHaveCount(2);
    $packageA->refresh();
    expect($packageA->repo_url)->toEqual($repoAttributesA['url']);
    expect($packageA->readme_source)->toEqual($repoAttributesA['source']);
    expect($packageA->readme)->toEqual($repoAttributesA['readme']);
    expect($packageA->latest_version)->toEqual($repoAttributesA['latest_version']);
    $packageB->refresh();
    expect($packageB->readme)->toEqual($packageBAttributes['readme']);
    expect($packageB->readme_source)->toEqual($packageBAttributes['readme_source']);
    expect($packageB->repo_url)->toEqual($packageBAttributes['repo_url']);
    expect($packageB->latest_version)->toEqual($packageBAttributes['latest_version']);
});

test('local data is not updated if there are no changes in the remote repo', function () {
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
    expect(Package::all())->toHaveCount(1);
    expect($package->updated_at->toDateTimeString())->toEqual($updatedAt->toDateTimeString());
});
