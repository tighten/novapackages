<?php

use App\Http\Resources\PackageDetailResource;
use App\Models\Package;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('can determine if the package is favorited by the authenticated user', function () {
    fakePackagistRequest();

    $package = Package::factory()->create();
    $user = User::factory()->create();
    $user->favoritePackage($package->id);

    $this->actingAs($user);
    $packageDetailResource = (PackageDetailResource::from($package));

    $this->assertTrue($packageDetailResource['is_favorite'], 'Failed asserting the package is favorited');
});

test('can determine if the package is unfavorited by the authenticated user', function () {
    fakePackagistRequest();

    $package = Package::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user);
    $packageDetailResource = (PackageDetailResource::from($package));

    $this->assertFalse($packageDetailResource['is_favorite'], 'Failed asserting the package is unfavorited');
});

test('return the count of favorites for a package', function () {
    fakePackagistRequest();

    $package = Package::factory()->create();
    $userA = User::factory()->create();
    $userA->favoritePackage($package->id);
    $userB = User::factory()->create();
    $userB->favoritePackage($package->id);

    $packageDetailResource = (PackageDetailResource::from($package));

    expect($packageDetailResource['favorites_count'])->toEqual(2);
});

test('includes whether package has been marked as unavailable', function () {
    fakePackagistRequest();

    $now = now();
    Carbon::setTestNow($now);

    $unavailablePackage = Package::factory()->create([
        'marked_as_unavailable_at' => now(),
    ]);
    $unavailablePackageDetailResource = (PackageDetailResource::from($unavailablePackage));
    expect($now)->toEqual($unavailablePackageDetailResource['marked_as_unavailable_at']);

    $validPackage = Package::factory()->create([
        'marked_as_unavailable_at' => null,
    ]);
    $validPackageDetailResource = (PackageDetailResource::from($validPackage));
    expect($validPackageDetailResource['marked_as_unavailable_at'])->toBeNull();
});

// Helpers
function fakePackagistRequest(): void
{
    Http::fake([
        'https://packagist.org/packages/*.json' => Http::response(),
    ]);
}
