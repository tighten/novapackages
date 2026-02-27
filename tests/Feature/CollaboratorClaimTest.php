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

    $this->assertNull($collaborator->user_id);

    $response = $this->be($user)->post(route('app.collaborators.claims.store', [$collaborator]));

    $this->assertEquals($user->id, $collaborator->refresh()->user->id);
});

test('users can claim a second collaborator', function () {
    Event::fake();

    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->create();
    $secondCollaborator = Collaborator::factory()->create();

    $response = $this->be($user)->post(route('app.collaborators.claims.store', [$collaborator]));
    $response = $this->be($user)->post(route('app.collaborators.claims.store', [$secondCollaborator]));

    $this->assertEquals($user->id, $collaborator->refresh()->user_id);
    $this->assertEquals($user->id, $secondCollaborator->refresh()->user_id);
});
