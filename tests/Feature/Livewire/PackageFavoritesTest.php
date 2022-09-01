<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\PackageFavorites;
use App\Package;
use App\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PackageFavoritesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function must_be_logged_in_to_favorite_package()
    {
        $this->expectException(AuthenticationException::class);

        Livewire::test(PackageFavorites::class)->call('toggleFavorite');
    }

    /** @test */
    function can_favorite_package()
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();

        $this->actingAs($user);
        Livewire::test(PackageFavorites::class, [
            'favoriteCount' => 0,
            'isFavorite' => false,
            'packageId' => $package->id,
        ])->call('toggleFavorite');

        $this->assertEquals($package->id, $user->favorites->pluck('package_id')->first());
    }

    /** @test */
    function can_unfavorite_package()
    {
        $user = User::factory()->create();
        $package = Package::factory()->create();

        $user->favoritePackage($package->id);

        $this->actingAs($user);
        Livewire::test(PackageFavorites::class, [
            'favoriteCount' => 0,
            'isFavorite' => true,
            'packageId' => $package->id,
        ])->call('toggleFavorite');

        $this->assertEmpty($user->favorites);
    }
}
