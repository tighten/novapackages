<?php

use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('user can claim a collaborator', function () {
    Event::fake();

    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->create();

    expect($collaborator->user_id)->toBeNull();

    $response = $this->be($user)->post(route('app.collaborators.claims.store', [$collaborator]));

    expect($collaborator->refresh()->user->id)->toEqual($user->id);
});

test('users can claim a second collaborator', function () {
    Event::fake();

    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->create();
    $secondCollaborator = Collaborator::factory()->create();

    $response = $this->be($user)->post(route('app.collaborators.claims.store', [$collaborator]));
    $response = $this->be($user)->post(route('app.collaborators.claims.store', [$secondCollaborator]));

    expect($collaborator->refresh()->user_id)->toEqual($user->id);
    expect($secondCollaborator->refresh()->user_id)->toEqual($user->id);
});
