<?php

namespace Tests\Feature;

use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use willvincent\Rateable\Rating;

class DeleteSelfAuthoredPackageRatingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function deleting_self_authored_package_ratings()
    {
        $packageAuthor = factory(User::class)->create();
        $otherUser = factory(User::class)->create();
        $package = factory(Package::class)->create();

        $invalidRating = new Rating(['rating' => 5]);
        $invalidRating->user_id = $packageAuthor->id;

        $validRating = new Rating(['rating' => 5]);
        $validRating->user_id = $otherUser->id;


        $package->ratings()->save($invalidRating);
        $package->ratings()->save($validRating);
        $this->assertEquals(2, $package->fresh()->ratings()->count());

        $this->artisan('purge:self-authored-package-ratings');

        $this->assertEquals(1, $package->fresh()->ratings()->count());
    }
}
