<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PopularityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function github_stars_influence_popularity()
    {
        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $user->collaborators()->save($collaborator);

        $collaborator->authoredPackages()->saveMany(Package::factory(20)->make());

        $popularPackages = Package::inRandomOrder()->take(10)->get();

        $popularPackages->each(function ($package) {
            $package->update(['github_stars' => 25]);
        });

        $popularScope = Package::popular()->take(10)->pluck('id')->toArray();
        $this->assertCount(10, array_intersect($popularScope, $popularPackages->pluck('id')->toArray()));
    }

    /** @test */
    public function packagist_downloads_influence_popularity()
    {
        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $user->collaborators()->save($collaborator);

        $collaborator->authoredPackages()->saveMany(Package::factory(20)->make());

        $popularPackages = Package::inRandomOrder()->take(10)->get();

        $popularPackages->each(function ($package) {
            $package->update(['packagist_downloads' => 25]);
        });

        $popularScope = Package::popular()->take(10)->pluck('id')->toArray();
        $this->assertCount(10, array_intersect($popularScope, $popularPackages->pluck('id')->toArray()));
    }
}
