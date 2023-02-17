<?php

namespace Tests\Feature;

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use App\Notifications\NotifyAuthorOfDisabledPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class DisableUnavailablePackageCommandTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function command_disables_unavailable_packages_after_30_days()
    {
        Notification::fake();

        $packageThatShouldBeDisabled = Package::factory()->create([
            'marked_as_unavailable_at' => today()->subDays(30),
            'author_id' => Collaborator::factory()->create([
                'user_id' => User::factory()->create()->id,
            ])->id,
        ]);

        $packageThatShouldNotBeDisabled = Package::factory()->create([
            'marked_as_unavailable_at' => today()->subDays(29),
            'author_id' => Collaborator::factory()->create([
                'user_id' => User::factory()->create()->id,
            ])->id,
        ]);

        $this->artisan('novapackages:disable-unavailable-packages');

        $this->assertTrue($packageThatShouldBeDisabled->refresh()->is_disabled);
        Notification::assertSentTo(
            $packageThatShouldBeDisabled->author->user,
            NotifyAuthorOfDisabledPackage::class,
        );

        $this->assertFalse($packageThatShouldNotBeDisabled->refresh()->is_disabled);
        Notification::assertNotSentTo(
            $packageThatShouldNotBeDisabled->author->user,
            NotifyAuthorOfDisabledPackage::class,
        );
    }
}
