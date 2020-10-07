<?php

namespace Tests\Feature\InternalApi;

use App\Package;
use App\Review;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use willvincent\Rateable\Rating;

class InternalApiReviewsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cant_post_review()
    {
        $package = factory(Package::class)->create();
        $review = factory(Review::class)->make();

        $response = $this->post(route('internalapi.reviews.store'), [
            'package_id' => $package->id,
            'review' => $review->content,
        ]);

        $this->assertEquals(route('login'), $response->getTargetUrl());
    }

    /** @test */
    public function authenticated_user_cannot_see_link_to_post_review_before_reviewing_package()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();

        $this->be($user)
            ->get('/packages/' . $package->composer_name)
            ->assertDontSee('Write Your Review Here');
    }

    /** @test */
    public function the_same_user_cant_add_two_reviews_to_a_package()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();
        $review = factory(Review::class)->make();

        $user->ratePackage($package->id, 3);

        $this->be($user)->post(route('internalapi.reviews.store'), [
            'package_id' => $package->id,
            'review' => $review->content,
        ]);

        $this->be($user)->post(route('internalapi.reviews.store'), [
            'package_id' => $package->id,
            'review' => $review->content,
        ]);

        $this->assertEquals(1, $package->reviews()->count());
    }

    /** @test */
    public function users_can_modify_their_reviews()
    {
        $package = factory(Package::class)->create();
        $user = factory(User::class)->create();

        $user->ratePackage($package->id, 3);

        $this->be($user)->post(route('internalapi.reviews.store'), [
            'package_id' => $package->id,
            'review' => 'Old Review Content',
        ]);

        $response = $this->be($user)->post(route('internalapi.reviews.store'), [
            'package_id' => $package->id,
            'review' => 'New Review Content',
        ]);

        $this->assertEquals('New Review Content', $package->reviews->first()->content);
    }

    /** @test */
    public function a_user_can_delete_their_review()
    {
        $review = factory(Review::class)->create();

        $this->be($review->user)
            ->delete(route('internalapi.reviews.delete', [$review->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id
        ]);
    }

    /** @test */
    public function a_user_cannot_delete_a_review_belonging_to_another_user()
    {
        $review = factory(Review::class)->create();
        $otherUser = factory(User::class)->create();

        $this->be($otherUser)
            ->delete(route('internalapi.reviews.delete', [$review->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id
        ]);
    }

    /** @test */
    public function an_admin_can_delete_another_users_review()
    {
        $review = factory(Review::class)->create();
        $adminUser = factory(User::class)->state('admin')->create();

        $this->be($adminUser)
            ->delete(route('internalapi.reviews.delete', [$review->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id
        ]);
    }
}
