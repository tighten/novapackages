<?php

use App\Models\Package;
use App\Models\Screenshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('screnshots not attached to packages that are older than one day are deleted', function () {
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
    expect($remainingScreenshots)->toHaveCount(2);
    expect($remainingScreenshots->contains($packageScreenshot))->toBeTrue();
    expect($remainingScreenshots->contains($newScreenshot))->toBeTrue();
    Storage::assertExists($packageScreenshot->path);
    Storage::assertExists($newScreenshot->path);
    Storage::assertMissing($abandonedScreenshot->path);
});
