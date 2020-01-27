<?php

namespace Tests\Feature\InternalApi;

use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InternalApiRatingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cant_post_ratings()
    {
        $package = factory(Package::class)->create();
        $response = $this->post(route('internalapi.ratings.store'), [
            'package_id' => $package->id,
            'rating' => 4,
        ]);

        $this->assertEquals(route('login'), $response->getTargetUrl());
    }

    /** @test */
    public function posting_a_rating_increases_the_packages_overall_rating()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();

        $this->be($user)->post(route('internalapi.ratings.store'), [
            'package_id' => $package->id,
            'rating' => 4,
        ]);

        $this->assertEquals(4, $package->average_rating);
    }

    /** @test */
    public function the_same_user_cant_add_two_ratings_to_a_package()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();

        $this->be($user)->post(route('internalapi.ratings.store'), [
            'package_id' => $package->id,
            'rating' => 4,
        ]);

        $this->be($user)->post(route('internalapi.ratings.store'), [
            'package_id' => $package->id,
            'rating' => 2,
        ]);

        $this->assertEquals(1, $package->ratings()->count());
    }

    /** @test */
    public function users_can_modify_their_ratings()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();

        $this->be($user)->post(route('internalapi.ratings.store'), [
            'package_id' => $package->id,
            'rating' => 4,
        ]);

        $this->be($user)->post(route('internalapi.ratings.store'), [
            'package_id' => $package->id,
            'rating' => 2,
        ]);

        $this->assertEquals(2, (int) $package->user_average_rating);
    }
}
