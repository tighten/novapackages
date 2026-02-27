<?php

use App\Models\User;
use App\Notifications\GithubAuthNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;


test('users not authenticated with github are notified', function () {
    $oldUser = User::factory()->create([
        'github_username' => null,
    ]);

    $newUser = User::factory()->create();

    Notification::fake();

    Artisan::call('githubauth:notify');

    Notification::assertSentTo($oldUser, GithubAuthNotification::class);
    Notification::assertNotSentTo($newUser, GithubAuthNotification::class);
});
