<?php

namespace Tests\Feature;

use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RatingCountableTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_counts_average_rating()
    {
        $package = Package::factory()->create();
        $users = User::factory(4)->create();

        $i = 1;
        foreach ($users as $user) {
            $user->ratePackage($package, $i++);
        }

        $this->assertEquals((1 + 2 + 3 + 4) / 4, $package->fresh()->average_rating);
    }

    /** @test */
    public function it_counts_each_rating_correctly()
    {
        $package = Package::factory()->create();
        $users = User::factory(15)->create();

        // Generate 1 5-star ratings, 2 4-star, etc.
        foreach (range(1, 5) as $i) {
            $users->shift()->ratePackage($package, 1);
        }

        foreach (range(1, 4) as $i) {
            $users->shift()->ratePackage($package, 2);
        }

        foreach (range(1, 3) as $i) {
            $users->shift()->ratePackage($package, 3);
        }

        foreach (range(1, 2) as $i) {
            $users->shift()->ratePackage($package, 4);
        }

        $users->shift()->ratePackage($package, 5);

        $this->assertEquals(1, $package->countFiveStarRatings());
        $this->assertEquals(2, $package->countFourStarRatings());
        $this->assertEquals(3, $package->countThreeStarRatings());
        $this->assertEquals(4, $package->countTwoStarRatings());
        $this->assertEquals(5, $package->countOneStarRatings());
    }

    /** @test */
    public function it_counts_each_rating_correctly_when_eager_loaded()
    {
        $package = Package::factory()->create();
        $users = User::factory(15)->create();

        // Generate 1 5-star ratings, 2 4-star, etc.
        foreach (range(1, 5) as $i) {
            $users->shift()->ratePackage($package, 1);
        }

        foreach (range(1, 4) as $i) {
            $users->shift()->ratePackage($package, 2);
        }

        foreach (range(1, 3) as $i) {
            $users->shift()->ratePackage($package, 3);
        }

        foreach (range(1, 2) as $i) {
            $users->shift()->ratePackage($package, 4);
        }

        $users->shift()->ratePackage($package, 5);

        $package = Package::with('ratings')->find($package->id);

        $this->assertEquals(1, $package->countFiveStarRatings());
        $this->assertEquals(2, $package->countFourStarRatings());
        $this->assertEquals(3, $package->countThreeStarRatings());
        $this->assertEquals(4, $package->countTwoStarRatings());
        $this->assertEquals(5, $package->countOneStarRatings());
    }

    /** @test */
    public function average_rating_rounded_to_1_decimal()
    {
        $package = Package::factory()->create();
        $users = User::factory(3)->create();
        $ratings = [3, 5, 5];

        $i = 0;
        foreach ($users as $user) {
            $user->ratePackage($package, $ratings[$i]);
            $i++;
        }

        $this->assertEquals(4.3, $package->fresh()->average_rating);
    }
}
