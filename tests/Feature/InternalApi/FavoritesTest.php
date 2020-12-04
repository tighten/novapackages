<?php

namespace Tests\Feature\InternalApi;

use App\Favorite;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_guest_user_can_not_favorite_a_package()
    {
        $package = Package::factory()->create();

        $response = $this->json('POST', route('internalapi.package.favorites.store', $package->id));

        $response->assertStatus(401);
        $this->assertCount(0, Favorite::where('package_id', $package->id)->get());
    }

    /** @test */
    public function an_authenticated_user_can_add_a_package_to_their_favorites()
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();

        $response = $this->actingAs($user)->json('POST', route('internalapi.package.favorites.store', $package));

        $this->assertCount(1, $user->favorites);
        $this->assertTrue($user->favorites()->first()->package->is($package));
    }

    /** @test */
    public function a_user_can_not_favorite_the_same_package_twice()
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();
        $user->favoritePackage($package->id);

        $response = $this->actingAs($user)->json('POST', route('internalapi.package.favorites.store', $package));

        $this->assertCount(1, $user->favorites);
        $this->assertTrue($user->favorites()->first()->package->is($package));
    }

    /** @test */
    public function a_user_can_remove_a_favorite()
    {
        $user = User::factory()->create();
        $packageA = Package::factory()->create();
        $packageB = Package::factory()->create();
        $user->favoritePackage($packageA->id);
        $user->favoritePackage($packageB->id);

        $response = $this->actingAs($user)->json('DELETE', route('internalapi.package.favorites.destroy', $packageB));

        $this->assertCount(1, $user->favorites);
        $this->assertTrue($user->favorites()->first()->package->is($packageA));
    }
}
