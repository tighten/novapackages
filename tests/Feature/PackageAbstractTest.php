<?php

namespace Tests\Feature;

use App\Http\Resources\PackageResource;
use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageAbstractTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function package_abstracts_default_to_truncated_readme_if_no_abstract()
    {
        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $user->collaborators()->save($collaborator);

        $package = $collaborator->authoredPackages()->save(Package::factory()->make([
            'abstract' => '',
            'readme' => 'Abcdef8181',
        ]));

        $response = $this->get(route('home'));

        $response->assertSee('Abcdef8181');

        // Let's check the resource, too; that check above is shaky
        $resource = PackageResource::from($package);

        $this->assertEquals('Abcdef8181', $resource['abstract']);
    }

    /** @test */
    public function long_package_readmes_are_truncated_to_190_characters_for_abstract()
    {
        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $user->collaborators()->save($collaborator);

        $package = $collaborator->authoredPackages()->save(Package::factory()->make([
            'abstract' => '',
            'readme' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam lorem erat, luctus at diam sed, dapibus facilisis purus. In laoreet enim nunc, ut pretium arcu scelerisque in. Nunc eu cursus nibh.', // 195 characters
        ]));

        $resource = PackageResource::from($package);

        $this->assertStringContainsString('Lorem ipsum', $resource['abstract']);
        $this->assertStringNotContainsString('nibh', $resource['abstract']);
    }

    /** @test */
    public function api_abstract_never_null()
    {
        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $user->collaborators()->save($collaborator);

        $package = $collaborator->authoredPackages()->save(Package::factory()->make([
            'abstract' => '',
        ]));

        $response = $this->get('api/recent');

        $this->assertNotEmpty($response->json()['data'][0]['abstract']);
    }
}
