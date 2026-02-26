<?php

namespace Tests\Feature\InternalApi;

use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InternalApiReviewsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function unauthenticated_users_cant_post_review(): void
    {
        $package = Package::factory()->create();
        $review = Review::factory()->make();

        $response = $this->post(route('internalapi.reviews.store'), [
            'package_id' => $package->id,
            'review' => $review->content,
        ]);

        $this->assertEquals(route('login'), $response->getTargetUrl());
    }

    #[Test]
    public function authenticated_user_cannot_see_link_to_post_review_before_reviewing_package(): void
    {
        Http::fake([
            'https://packagist.org/packages/*.json' => Http::response(),
        ]);

        $package = Package::factory()->create();
        $user = User::factory()->create();

        $this->be($user)
            ->get('/packages/' . $package->composer_name)
            ->assertDontSee('Write Your Review Here');
    }

    #[Test]
    public function the_same_user_cant_add_two_reviews_to_a_package(): void
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

    #[Test]
    public function users_can_modify_their_reviews(): void
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

    #[Test]
    public function a_user_can_delete_their_review(): void
    {
        $review = Review::factory()->create();

        $this->be($review->user)
            ->delete(route('internalapi.reviews.delete', [$review->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }

    #[Test]
    public function a_user_cannot_delete_a_review_belonging_to_another_user(): void
    {
        $review = Review::factory()->create();
        $otherUser = User::factory()->create();

        $this->be($otherUser)
            ->delete(route('internalapi.reviews.delete', [$review->id]))
            ->assertForbidden();

        $this->assertDatabaseHas('reviews', [
            'id' => $review->id,
        ]);
    }

    #[Test]
    public function an_admin_can_delete_another_users_review(): void
    {
        $review = Review::factory()->create();
        $adminUser = User::factory()->admin()->create();

        $this->be($adminUser)
            ->delete(route('internalapi.reviews.delete', [$review->id]))
            ->assertSuccessful();

        $this->assertDatabaseMissing('reviews', [
            'id' => $review->id,
        ]);
    }
}
