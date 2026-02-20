<?php

namespace Tests\Feature;

use App\Screenshot;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ScreenshotUploadTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_users_can_upload_a_screenshot(): void
    {
        Storage::fake();

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $response = $this->actingAs($userB)->json('POST', route('app.screenshot-uploads.store'), [
            'screenshot' => File::image('screenshot.jpg'),
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(201);
        $this->assertcount(1, Screenshot::all());
        Storage::assertExists($response->json()['path']);
        $this->assertEquals($userB->id, Screenshot::first()->uploader->id);
        $response->assertJsonStructure([
            'id',
            'public_url',
        ]);
    }

    #[Test]
    public function guest_users_can_not_upload_screenshot(): void
    {
        Storage::fake();

        $response = $this->json('POST', route('app.screenshot-uploads.store'), [
            'screenshot' => File::image('screenshot.jpg'),
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(401);
        $this->assertcount(0, Screenshot::all());
    }

    #[Test]
    public function the_uploaded_screenshot_must_be_smaller_than_2mb(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->json('POST', route('app.screenshot-uploads.store'), [
            'screenshot' => File::create('screenshot.jpg', 2049),
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('screenshot');
        $this->assertCount(0, Screenshot::all());
    }

    #[Test]
    public function the_upload_screenshot_must_be_an_image(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->json('POST', route('app.screenshot-uploads.store'), [
            'screenshot' => File::create('invlaid-image.pdf'),
        ], [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('screenshot');
        $this->assertCount(0, Screenshot::all());
    }
}
