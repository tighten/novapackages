<?php

namespace Tests\Feature;

use App\Package;
use App\Review;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_inform_whether_theyve_rated_a_package()
    {
        $user = factory(User::class)->create();
        $package = factory(Package::class)->create();

        $this->assertFalse($user->hasReviewed($package));

        $review = factory(Review::class)->create([
            'user_id' => $user->id,
            'package_id' => $package->id,
        ]);

        $this->assertTrue($user->refresh()->hasReviewed($package));
    }
}
