<?php

use App\Jobs\SyncPackageRepositoryData;
use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('an authenticated user can request a refresh of a packages repository data', function () {
    Bus::fake();

    $package = Package::factory()->make();
    $collaborator = Collaborator::factory()->make();
    $user = User::factory()->create();
    $user->collaborators()->save($collaborator);
    $collaborator->authoredPackages()->save($package);

    $response = $this->actingAs($user)->json('POST', route('app.packages.repository.refresh', $package));

    $response->assertSuccessful();
    Bus::assertDispatched(SyncPackageRepositoryData::class, function ($job) use ($package) {
        return $job->package->id === $package->id;
    });
});

test('a guest user can not request a refresh of a packages repository data', function () {
    Bus::fake();

    $package = Package::factory()->create();

    $response = $this->json('POST', route('app.packages.repository.refresh', $package));

    $response->assertStatus(401);
    Bus::assertNotDispatched(SyncPackageRepositoryData::class);
});
