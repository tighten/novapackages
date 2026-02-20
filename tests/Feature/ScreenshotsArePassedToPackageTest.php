<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\Screenshot;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ScreenshotsArePassedToPackageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function a_packages_screenshots_are_passed_to_the_view(): void
    {
        Http::fake(['https://packagist.org/packages/tightenco/bae.json' => Http::response()]);

        $packageNamespace = 'tightenco';
        $packageName = 'bae';
        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->make();
        $user->collaborators()->save($collaborator);
        $packageA = Package::factory()->make([
            'composer_name' => "{$packageNamespace}/{$packageName}",
        ]);
        $collaborator->authoredPackages()->save($packageA);
        $screenshotA = Screenshot::factory()->create(['uploader_id' => $user->id]);
        $packageA->screenshots()->save($screenshotA);
        $packageB = Package::factory()->create();
        $screenshotB = Screenshot::factory()->create(['uploader_id' => $user->id]);
        $packageB->screenshots()->save($screenshotB);

        $response = $this->actingAs($user)
            ->get(route('packages.show', ['namespace' => $packageNamespace, 'name' => $packageName]));
        $response->assertSuccessful();

        // Screenshots are now loaded within the Livewire component, not passed as view data
        $package = $response->viewData('package');
        $this->assertCount(1, $package->screenshots);
        $this->assertTrue($package->screenshots->contains($screenshotA));
        $this->assertFalse($package->screenshots->contains($screenshotB));
    }
}
