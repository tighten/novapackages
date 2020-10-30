<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Notifications\RemindAuthorOfUnavailablePackage;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RemindPackageAuthorOfUnavailablePackageCommandTest extends TestCase
{

    use RefreshDatabase;

    /**
     * @test
     */
    public function command_sends_reminder_to_package_author_after_14_days()
    {

        Notification::fake();

        $packageThatShouldReceiveNotification = Package::factory()->create([
            'marked_as_unavailable_at' => today()->subWeeks(2),
            'author_id' => Collaborator::factory()->create([
                'user_id' => User::factory()->create()->id
            ])->id
        ]);

        $packageThatShouldNotReceiveNotification = Package::factory()->create([
            'marked_as_unavailable_at' => today()->subWeeks(1),
            'author_id' => Collaborator::factory()->create([
                'user_id' => User::factory()->create()->id
            ])->id
        ]);

        $disabledPackage = Package::factory()->create([
            'marked_as_unavailable_at' => today()->subWeeks(1),
            'is_disabled' => 1,
            'author_id' => Collaborator::factory()->create([
                'user_id' => User::factory()->create()->id
            ])->id
        ]);

        $this->artisan('novapackages:send-unavailable-package-followup');

        Notification::assertSentTo(
            $packageThatShouldReceiveNotification->author->user,
            RemindAuthorOfUnavailablePackage::class,
        );

        Notification::assertNotSentTo(
            $packageThatShouldNotReceiveNotification->author->user,
            RemindAuthorOfUnavailablePackage::class,
        );

        Notification::assertNotSentTo(
            $disabledPackage->author->user,
            RemindAuthorOfUnavailablePackage::class,
        );
    }
}
