<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\PackageDetailResource;
use App\Package;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageDetailResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_determine_if_the_package_is_favorited_by_the_authenticated_user()
    {
        $package = Package::factory()->create();
        $user = User::factory()->create();
        $user->favoritePackage($package->id);

        $this->actingAs($user);
        $packageDetailResource = (PackageDetailResource::from($package));

        $this->assertTrue($packageDetailResource['is_favorite'], 'Failed asserting the package is favorited');
    }

    /** @test */
    public function can_determine_if_the_package_is_unfavorited_by_the_authenticated_user()
    {
        $package = Package::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user);
        $packageDetailResource = (PackageDetailResource::from($package));

        $this->assertFalse($packageDetailResource['is_favorite'], 'Failed asserting the package is unfavorited');
    }

    /** @test */
    public function return_the_count_of_favorites_for_a_package()
    {
        $package = Package::factory()->create();
        $userA = User::factory()->create();
        $userA->favoritePackage($package->id);
        $userB = User::factory()->create();
        $userB->favoritePackage($package->id);

        $packageDetailResource = (PackageDetailResource::from($package));

        $this->assertEquals(2, $packageDetailResource['favorites_count']);
    }

    /** @test */
    public function includes_whether_package_has_been_marked_as_unavailable()
    {
        $now = now();
        Carbon::setTestNow($now);

        $unavailablePackage = Package::factory()->create([
            'marked_as_unavailable_at' => now()
        ]);
        $unavailablePackageDetailResource = (PackageDetailResource::from($unavailablePackage));
        $this->assertEquals($unavailablePackageDetailResource['marked_as_unavailable_at'], $now);

        $validPackage = Package::factory()->create([
            'marked_as_unavailable_at' => null
        ]);
        $validPackageDetailResource = (PackageDetailResource::from($validPackage));
        $this->assertNull($validPackageDetailResource['marked_as_unavailable_at']);
    }
}
