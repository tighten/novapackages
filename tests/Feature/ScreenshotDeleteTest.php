<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\Screenshot;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ScreenshotDeleteTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_screenshot_can_be_deleted_by_the_person_who_uploaded_it(): void
    {
        Storage::fake();

        $user = User::factory()->create();
        $screenshotA = Screenshot::factory()->create([
            'uploader_id' => $user->id,
            'path' => File::create('screenshot.jpg')->store('screenshots'),
        ]);
        $screenshotB = Screenshot::factory()->create([
            'uploader_id' => $user->id,
            'path' => File::create('screenshot.jpg')->store('screenshots'),
        ]);

        $response = $this->actingAs($user)->json('DELETE', route('app.screenshot-uploads.destroy', $screenshotA), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertSuccessful();
        $screenshots = Screenshot::all();
        $this->assertCount(1, $screenshots);
        $this->assertTrue($screenshots->contains($screenshotB));
        $this->assertFalse($screenshots->contains($screenshotA));
        Storage::assertMissing($screenshotA->path);
    }

    /** @test */
    public function a_package_collaborator_can_delete_an_attached_screenshot(): void
    {
        Storage::fake();

        list($package, $collaboratorUser) = $this->createPackageWithUser();
        $uploader = User::factory()->create();
        $packageScreenshot = Screenshot::factory()->create([
            'uploader_id' => $uploader->id,
            'path' => File::create('screenshotA.jpg')->store('screenshots'),
        ]);
        $package->screenshots()->save($packageScreenshot);

        $response = $this->actingAs($collaboratorUser)->json('DELETE', route('app.screenshot-uploads.destroy', $packageScreenshot), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertSuccessful();
        $this->assertCount(0, Screenshot::all());
        Storage::assertMissing($packageScreenshot->path);
    }

    /** @test */
    public function an_unauthorized_user_can_not_delete_a_screenshot(): void
    {
        Storage::fake();

        $user = User::factory()->create();
        $screenshot = Screenshot::factory()->create([
            'path' => File::create('screenshot.jpg')->store('screenshots'),
        ]);

        $response = $this->actingAs($user)->json('DELETE', route('app.screenshot-uploads.destroy', $screenshot), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(403);
        $screenshots = Screenshot::all();
        $this->assertCount(1, $screenshots);
        $this->assertTrue($screenshots->contains($screenshot));
        Storage::assertExists($screenshot->path);
    }

    /** @test */
    public function a_guest_user_can_not_delete_a_screenshot(): void
    {
        Storage::fake();

        $screenshot = Screenshot::factory()->create([
            'path' => File::create('screenshotA.jpg')->store('screenshots'),
        ]);

        $response = $this->json('DELETE', route('app.screenshot-uploads.destroy', $screenshot), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(401);
        $this->assertCount(1, Screenshot::all());
        $this->assertTrue(Screenshot::first()->is($screenshot));
        Storage::assertExists($screenshot->path);
    }

    private function createPackageWithUser()
    {
        $package = Package::factory()->make();
        $collaborator = Collaborator::factory()->make();
        $user = User::factory()->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);

        return [$package, $user];
    }
}
