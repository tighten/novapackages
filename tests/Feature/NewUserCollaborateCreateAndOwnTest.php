<?php

use App\Events\NewUserSignedUp;
use App\Http\Remotes\GitHub;
use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Mockery as m;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

it('creates a collaborator for new users', function () {
    Notification::fake();

    $github = m::mock(GitHub::class)->shouldIgnoreMissing();
    app()->instance(GitHub::class, $github);

    $user = User::factory()->create();

    event(new NewUserSignedUp($user));

    $this->assertEquals(1, Collaborator::count());
});

it('claims collaborator for new users if matching by github username', function () {
    Notification::fake();

    $collaborator = Collaborator::factory()->create([
        'github_username' => 'josecanhelp',
    ]);

    $user = User::factory()->create([
        'github_username' => 'josecanhelp',
    ]);

    event(new NewUserSignedUp($user));

    $this->assertEquals(1, Collaborator::count());
    $this->assertEquals($user->id, $collaborator->refresh()->user_id);
});
