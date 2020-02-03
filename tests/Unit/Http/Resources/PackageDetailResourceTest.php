<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\PackageDetailResource;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageDetailResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_determine_if_the_package_is_favorited_by_the_authenticated_user()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();
        $user->favoritePackage($package->id);

        $this->actingAs($user);
        $packageDetailResource = (PackageDetailResource::from($package));

        $this->assertTrue($packageDetailResource['is_favorite'], 'Failed asserting the package is favorited');
    }

    /** @test */
    public function can_determine_if_the_package_is_unfavorited_by_the_authenticated_user()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $packageDetailResource = (PackageDetailResource::from($package));

        $this->assertFalse($packageDetailResource['is_favorite'], 'Failed asserting the package is unfavorited');
    }

    /** @test */
    public function return_the_count_of_favorites_for_a_package()
    {
        $package = factory(Package::class)->create();
        $userA = factory(User::class)->create();
        $userA->favoritePackage($package->id);
        $userB = factory(User::class)->create();
        $userB->favoritePackage($package->id);

        $packageDetailResource = (PackageDetailResource::from($package));

        $this->assertEquals(2, $packageDetailResource['favorites_count']);
    }
}
