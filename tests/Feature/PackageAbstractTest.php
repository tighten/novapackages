<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Http\Resources\PackageResource;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageAbstractTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function package_abstracts_default_to_truncated_readme_if_no_abstract()
    {
        $user = factory(User::class)->create();
        $collaborator = factory(Collaborator::class)->create();
        $user->collaborators()->save($collaborator);

        $package = $collaborator->authoredPackages()->save(factory(Package::class)->make([
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
        $user = factory(User::class)->create();
        $collaborator = factory(Collaborator::class)->create();
        $user->collaborators()->save($collaborator);

        $package = $collaborator->authoredPackages()->save(factory(Package::class)->make([
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
        $user = factory(User::class)->create();
        $collaborator = factory(Collaborator::class)->create();
        $user->collaborators()->save($collaborator);

        $package = $collaborator->authoredPackages()->save(factory(Package::class)->make([
            'abstract' => '',
        ]));

        $response = $this->get('api/recent');

        $this->assertNotEmpty($response->json()['data'][0]['abstract']);
    }
}
