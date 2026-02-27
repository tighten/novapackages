<?php

use App\Models\Collaborator;
use App\Models\Package;
use App\Models\User;
use App\Notifications\NotifyAuthorOfDisabledPackage;
use Illuminate\Support\Facades\Notification;

test('command disables unavailable packages after 30 days', function () {

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

    expect($packageThatShouldBeDisabled->refresh()->is_disabled)->toBeTrue();
    Notification::assertSentTo(
        $packageThatShouldBeDisabled->author->user,
        NotifyAuthorOfDisabledPackage::class,
    );

    expect($packageThatShouldNotBeDisabled->refresh()->is_disabled)->toBeFalse();
    Notification::assertNotSentTo(
        $packageThatShouldNotBeDisabled->author->user,
        NotifyAuthorOfDisabledPackage::class,
    );
});
