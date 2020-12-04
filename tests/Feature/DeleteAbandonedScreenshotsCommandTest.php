<?php

namespace Tests\Feature;

use App\Package;
use App\Screenshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DeleteAbandonedScreenshotsCommandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function screnshots_not_attached_to_packages_that_are_older_than_one_day_are_deleted()
    {
        Storage::fake();

        $package = Package::factory()->create();
        $packageScreenshot = Screenshot::factory()->create([
            'path' => Storage::putFile('screenshots', File::create('screenshot.jpg')),
            'created_at' => Carbon::now()->subHours(25),
        ]);
        $package->screenshots()->save($packageScreenshot);
        $abandonedScreenshot = Screenshot::factory()->create([
            'path' => Storage::putFile('screenshots', File::create('screenshot.jpg')),
            'created_at' => Carbon::now()->subHours(25),
        ]);
        $newScreenshot = Screenshot::factory()->create([
            'path' => Storage::putFile('screenshots', File::create('screenshot.jpg')),
            'created_at' => Carbon::now(),
        ]);

        $this->artisan('purge:abandonedscreenshots');

        $remainingScreenshots = Screenshot::get();
        $this->assertCount(2, $remainingScreenshots);
        $this->assertTrue($remainingScreenshots->contains($packageScreenshot));
        $this->assertTrue($remainingScreenshots->contains($newScreenshot));
        Storage::assertExists($packageScreenshot->path);
        Storage::assertExists($newScreenshot->path);
        Storage::assertMissing($abandonedScreenshot->path);
    }
}
