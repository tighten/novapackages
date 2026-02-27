<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use App\Notifications\RemindAuthorOfUnavailablePackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('command sends reminder to package author after 14 days', function () {

    Notification::fake();

    $packageThatShouldReceiveNotification = Package::factory()->create([
        'marked_as_unavailable_at' => today()->subWeeks(2),
        'author_id' => Collaborator::factory()->create([
            'user_id' => User::factory()->create()->id,
        ])->id,
    ]);

    $packageThatShouldNotReceiveNotification = Package::factory()->create([
        'marked_as_unavailable_at' => today()->subWeeks(1),
        'author_id' => Collaborator::factory()->create([
            'user_id' => User::factory()->create()->id,
        ])->id,
    ]);

    $disabledPackage = Package::factory()->create([
        'marked_as_unavailable_at' => today()->subWeeks(1),
        'is_disabled' => 1,
        'author_id' => Collaborator::factory()->create([
            'user_id' => User::factory()->create()->id,
        ])->id,
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
});
