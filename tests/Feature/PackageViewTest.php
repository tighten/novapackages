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
        $packageA = factory(Package::class)->make([
            'composer_name' => "{$packageNamespace}/{$packageName}",
        ]);
        $collaborator = factory(Collaborator::class)->make();
        $user = factory(User::class)->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($packageA);
        $screenshot = factory(Screenshot::class)->create(['uploader_id' => $user->id]);
        $packageA->screenshots()->save($screenshot);
        $packageB = factory(Package::class)->create();

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
        $packageA = factory(Package::class)->make([
            'composer_name' => "{$packageNamespace}/{$packageName}",
        ]);
        $collaborator = factory(Collaborator::class)->make();
        $user = factory(User::class)->create();
        $user->collaborators()->save($collaborator);
        $collaborator->authoredPackages()->save($packageA);

        $response = $this->actingAs($user)
            ->get(route('packages.show', ['namespace' => $packageA->id]));

        $response->assertRedirect(route('packages.show', ['namespace' => $packageNamespace, 'name' => $packageName]));
    }
}
