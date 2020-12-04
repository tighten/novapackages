<?php

namespace Tests\Feature\InternalApi;

use App\Package;
use App\Review;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InternalApiReviewsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cant_post_review()
    {
        $package = Package::factory()->create();
        $review = Review::factory()->make();

        $response = $this->post(route('internalapi.reviews.store'), [
            'package_id' => $package->id,
            'review' => $review->content,
        ]);

        $this->assertEquals(route('login'), $response->getTargetUrl());
    }

    /** @test */
    public function authenticated_user_cannot_see_link_to_post_review_before_reviewing_package()
    {
        $package = Package::factory()->create();
        $user = User::factory()->create();

        $this->be($user)
            ->get('/packages/' . $package->composer_name)
            ->assertDontSee('Write Your Review Here');
    }

    /** @test */
    public function the_same_user_cant_add_two_reviews_to_a_package()
    {
        $package = Package::factory()->create();
        $user = User::factory()->create();
        $review = Review::factory()->make();

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
        $package = Package::factory()->create();
        $user = User::factory()->create();

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
        $review = Review::factory()->create();

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
        $review = Review::factory()->create();
        $otherUser = User::factory()->create();

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
        $review = Review::factory()->create();
        $adminUser = User::factory()->admin()->create();

        $this->be($adminUser)
            ->delete(route('internalapi.reviews.delete', [$review->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id
        ]);
    }
}
