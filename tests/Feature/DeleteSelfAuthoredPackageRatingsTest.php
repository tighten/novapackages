<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use willvincent\Rateable\Rating;

class DeleteSelfAuthoredPackageRatingsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function deleting_self_authored_package_ratings()
    {
        $packageAuthor = User::factory()->create();
        // We are creating two collaborators here and setting the
        // second one on the author to ensure the user_id on the rating
        // is different from the author_id on the package.
        $collaborators = Collaborator::factory(2)->create();
        $packageAuthor->collaborators()->save($collaborators->last());
        $otherUser = User::factory()->create();
        $package = Package::factory()->create([
            'author_id' => $collaborators->last()->id,
        ]);

        $invalidRating = new Rating(['rating' => 5]);
        $invalidRating->user_id = $packageAuthor->id;

        $validRating = new Rating(['rating' => 5]);
        $validRating->user_id = $otherUser->id;

        $package->ratings()->save($invalidRating);
        $package->ratings()->save($validRating);
        $this->assertEquals(2, $package->fresh()->ratings()->count());

        $this->artisan('purge:self-authored-package-ratings');

        tap($package->fresh(), function ($package) use ($otherUser) {
            $this->assertEquals(1, $package->ratings()->count());
            $this->assertEquals($otherUser->id, $package->ratings->first()->user_id);
        });
    }

    /** @test */
    public function deleting_self_contributed_package_ratings()
    {
        $packageAuthor = User::factory()->create();
        $packageContributer = User::factory()->create();
        $collaborator = Collaborator::factory()->create([
            'user_id' => $packageContributer->id,
        ]);
        $otherUser = User::factory()->create();
        $package = Package::factory()->create([
            'author_id' => $packageAuthor->id,
        ]);
        $package->contributors()->sync([$collaborator->id]);

        $invalidRating = new Rating(['rating' => 5]);
        $invalidRating->user_id = $packageContributer->id;

        $validRating = new Rating(['rating' => 5]);
        $validRating->user_id = $otherUser->id;

        $package->ratings()->save($invalidRating);
        $package->ratings()->save($validRating);
        $this->assertEquals(2, $package->fresh()->ratings()->count());

        $this->artisan('purge:self-authored-package-ratings');

        tap($package->fresh(), function ($package) use ($otherUser) {
            $this->assertEquals(1, $package->ratings()->count());
            $this->assertEquals($otherUser->id, $package->ratings->first()->user_id);
        });
    }
}
