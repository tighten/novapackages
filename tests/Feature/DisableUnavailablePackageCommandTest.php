<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisableUnavailablePackageCommandTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     */
    public function command_disables_unavailable_packages_after_30_days()
    {
        $packageThatShouldBeDisabled = factory(Package::class)->create([
            'marked_as_unavailable_at' => today()->subDays(30),
            'author_id' => factory(Collaborator::class)->create([
                'user_id' => factory(User::class)->create()->id
            ])->id
        ]);

        $packageThatShouldNotBeDisabled = factory(Package::class)->create([
            'marked_as_unavailable_at' => today()->subDays(29),
            'author_id' => factory(Collaborator::class)->create([
                'user_id' => factory(User::class)->create()->id
            ])->id
        ]);

        $this->artisan('novapackages:disable-unavailable-packages');

        $this->assertTrue($packageThatShouldBeDisabled->refresh()->is_disabled);
        $this->assertFalse($packageThatShouldNotBeDisabled->refresh()->is_disabled);
    }
}
