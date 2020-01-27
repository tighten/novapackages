<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\PackageResource;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PackageResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_abstract_is_returned_if_the_resource_has_an_abstract()
    {
        $abstract = 'This is the test abstract';
        $package = factory(Package::class)->create([
            'abstract' => $abstract,
            'description' => 'This is the test description',
        ]);

        $packageResource = (PackageResource::from($package));

        $this->assertEquals($abstract, $packageResource['abstract']);
    }

    /** @test */
    public function an_abstractified_value_is_returned_when_the_abstract_is_null()
    {
        $package = factory(Package::class)->create([
            'abstract' => null,
        ]);

        $packageResource = (PackageResource::from($package));

        $this->assertNotNull($packageResource['abstract']);
        $this->assertEquals($packageResource['abstract'], $package->abstract);
    }

    /** @test */
    public function can_determine_if_the_package_is_favorited_by_the_authenticated_user()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();
        $user->favoritePackage($package->id);

        $this->actingAs($user);
        $packageResource = (PackageResource::from($package));

        $this->assertTrue($packageResource['is_favorite'], 'Failed asserting the package is favorited');
    }

    /** @test */
    public function can_determine_if_the_package_is_unfavorited_by_the_authenticated_user()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();

        $this->actingAs($user);
        $packageResource = (PackageResource::from($package));

        $this->assertFalse($packageResource['is_favorite'], 'Failed asserting the package is unfavorited');
    }

    /** @test */
    public function return_the_count_of_favorites_for_a_package()
    {
        $package = factory(Package::class)->create();
        $userA = factory(User::class)->create();
        $userA->favoritePackage($package->id);
        $userB = factory(User::class)->create();
        $userB->favoritePackage($package->id);

        $packageResource = (PackageResource::from($package));

        $this->assertEquals(2, $packageResource['favorites_count']);
    }
}
