<?php

namespace Tests\Feature;

use App\Notifications\GithubAuthNotification;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GithubAuthNotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_not_authenticated_with_github_are_notified()
    {
        $oldUser = factory(User::class)->create([
            'github_username' => null,
        ]);

        $newUser = factory(User::class)->create();

        Notification::fake();

        Artisan::call('githubauth:notify');

        Notification::assertSentTo($oldUser, GithubAuthNotification::class);
        Notification::assertNotSentTo($newUser, GithubAuthNotification::class);
    }
}
