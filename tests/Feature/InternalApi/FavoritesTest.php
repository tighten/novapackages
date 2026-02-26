<?php

namespace Tests\Feature\InternalApi;

use App\Models\Favorite;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FavoritesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_guest_user_can_not_favorite_a_package(): void
    {
        $package = Package::factory()->create();

        $response = $this->json('POST', route('internalapi.package.favorites.store', $package->id));

        $response->assertStatus(401);
        $this->assertCount(0, Favorite::where('package_id', $package->id)->get());
    }

    #[Test]
    public function an_authenticated_user_can_add_a_package_to_their_favorites(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();

        $response = $this->actingAs($user)->json('POST', route('internalapi.package.favorites.store', $package));

        $this->assertCount(1, $user->favorites);
        $this->assertTrue($user->favorites()->first()->package->is($package));
    }

    #[Test]
    public function a_user_can_not_favorite_the_same_package_twice(): void
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();
        $user->favoritePackage($package->id);

        $response = $this->actingAs($user)->json('POST', route('internalapi.package.favorites.store', $package));

        $this->assertCount(1, $user->favorites);
        $this->assertTrue($user->favorites()->first()->package->is($package));
    }

    #[Test]
    public function a_user_can_remove_a_favorite(): void
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
