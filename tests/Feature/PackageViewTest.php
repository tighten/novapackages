<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\Screenshot;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PackageViewTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_the_show_package_page()
    {
        $packageNamespace = 'tightenco';
        $packageName = 'bae';
        $packageA = Package::factory()->make([
            'composer_name' => "{$packageNamespace}/{$packageName}",
        ]);
        $collaborator = Collaborator::factory()->make();
        $user = User::factory()->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($packageA);
        $screenshot = Screenshot::factory()->create(['uploader_id' => $user->id]);
        $packageA->screenshots()->save($screenshot);
        $packageB = Package::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('packages.show', ['namespace' => $packageNamespace, 'name' => $packageName]));

        $response->assertSuccessful();
        $response->assertViewHas('package');
        $response->assertViewHas('screenshots');
    }

    /** @test */
    public function legacy_package_id_lookup_redirects_to_namespace_search()
    {
        $packageNamespace = 'tightenco';
        $packageName = 'bae';
        $package = Package::factory()->make([
            'composer_name' => "{$packageNamespace}/{$packageName}",
        ]);
        $collaborator = Collaborator::factory()->make();
        $user = User::factory()->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($package);

        $response = $this->actingAs($user)
            ->get(route('packages.show-id', ['package' => $package->id]));

        $response->assertRedirect(route('packages.show', ['namespace' => $packageNamespace, 'name' => $packageName]));
    }
}
