<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContributorCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function packages_can_have_contributors()
    {
        $user = factory(User::class)->create();
        $contributor = factory(Collaborator::class)->create();
        $user->collaborators()->save($contributor);

        $package = $contributor->contributedPackages()->save(factory(Package::class)->make());

        $this->assertEquals($contributor->id, $package->contributors()->first()->id);
    }
}
