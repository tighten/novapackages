<?php

use App\Events\CollaboratorCreated;
use App\Http\Remotes\GitHub;
use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery as m;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);
beforeEach(function () {
    Notification::fake();
});


test('users can create collaborators', function () {
    Event::fake();

    $github = m::mock(GitHub::class)->shouldIgnoreMissing();
    app()->instance(GitHub::class, $github);

    $user = User::factory()->create();

    $this->be($user)->post(route('app.collaborators.store'), [
        'name' => 'Matt Stauffer',
        'github_username' => 'mattstauffer',
        'url' => 'https://mattstauffer.com/',
    ]);

    Event::assertDispatched(CollaboratorCreated::class);
    expect(Collaborator::count())->toEqual(1);
});

test('only one collaborator is allowed per github username', function () {
    $github = m::mock(GitHub::class)->shouldIgnoreMissing();
    app()->instance(GitHub::class, $github);

    $user = User::factory()->create();

    $this->be($user)->post(route('app.collaborators.store'), [
        'name' => 'Matt Stauffer',
        'github_username' => 'mattstauffer',
        'url' => 'https://mattstauffer.com/',
    ]);

    $this->be($user)->post(route('app.collaborators.store'), [
        'name' => 'Stat Shmauffer',
        'github_username' => 'mattstauffer',
        'url' => 'https://statshmauffer.com/',
    ]);

    expect(Collaborator::count())->toEqual(1);
});

test('collaborators receive avatar url from github upon creation', function () {
    $github = m::mock(GitHub::class);
    $github->shouldReceive('user')
        ->with('not_a_real_github_username')
        ->andReturn(['avatar_url' => 'http://www.image.com/']);

    app()->instance(GitHub::class, $github);

    $user = User::factory()->create();

    $this->be($user)->post(route('app.collaborators.store'), [
        'name' => 'Matt Stauffer',
        'github_username' => 'not_a_real_github_username',
        'url' => 'https://mattstauffer.com/',
    ]);

    expect(Collaborator::first()->avatar)->toEqual('http://www.image.com/');
});
