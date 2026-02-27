<?php

use App\Events\NewUserSignedUp;
use App\Http\Remotes\GitHub;
use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Mockery as m;

it('creates a collaborator for new users', function () {
    Notification::fake();

    $github = m::mock(GitHub::class)->shouldIgnoreMissing();
    app()->instance(GitHub::class, $github);

    $user = User::factory()->create();

    event(new NewUserSignedUp($user));

    expect(Collaborator::count())->toEqual(1);
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

    expect(Collaborator::count())->toEqual(1);
    expect($collaborator->refresh()->user_id)->toEqual($user->id);
});
