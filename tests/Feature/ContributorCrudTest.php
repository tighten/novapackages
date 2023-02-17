<?php

namespace Tests\Feature;

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContributorCrudTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function packages_can_have_contributors()
    {
        $user = User::factory()->create();
        $contributor = Collaborator::factory()->create();
        $user->collaborators()->save($contributor);

        $package = $contributor->contributedPackages()->save(Package::factory()->make());

        $this->assertEquals($contributor->id, $package->contributors()->first()->id);
    }
}
