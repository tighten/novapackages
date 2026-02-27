<?php

use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('updating collaborator names when the user name changes', function () {
    $user = User::factory()->create([
        'name' => 'Caleb Dume',
        'github_username' => 'calebdume',
    ]);
    $collaborator = Collaborator::factory()->make([
        'name' => 'Caleb Dume',
        'github_username' => 'calebdume',
    ]);
    $user->collaborators()->save($collaborator);

    $user->update(['name' => 'Kanan Jarrus']);

    $this->assertEquals('Kanan Jarrus', $collaborator->fresh()->name);
});

test('updating collaborator names only updates where same github username', function () {
    $user = User::factory()->create([
        'name' => 'Caleb Dume',
        'github_username' => 'calebdume',
    ]);
    $collaborator = Collaborator::factory()->make([
        'name' => 'Caleb Dume',
        'github_username' => 'calebdume',
    ]);
    $newCollaborator = Collaborator::factory()->make([
        'name' => 'Ezra Bridger',
        'github_username' => 'ezrabridger',
    ]);
    $user->collaborators()->save($collaborator);
    $user->collaborators()->save($newCollaborator);

    $user->update(['name' => 'Kanan Jarrus']);

    $this->assertEquals('Kanan Jarrus', $collaborator->fresh()->name);
    $this->assertEquals('Ezra Bridger', $newCollaborator->fresh()->name);
});

test('updating collaborator github usernames when the user github username changes', function () {
    $user = User::factory()->create([
        'github_user_id' => 123,
        'github_username' => 'calebdume',
    ]);
    $collaborator = Collaborator::factory()->make([
        'github_user_id' => 123,
        'github_username' => 'calebdume',
    ]);
    $user->collaborators()->save($collaborator);

    $user->update(['github_username' => 'kananjarrus']);

    $this->assertEquals('kananjarrus', $collaborator->fresh()->github_username);
});

test('updating collaborator github usernames only updates where same github user id', function () {
    $user = User::factory()->create([
        'github_user_id' => 123,
        'github_username' => 'calebdume',
    ]);
    $collaborator = Collaborator::factory()->make([
        'github_user_id' => 123,
        'github_username' => 'calebdume',
    ]);
    $newCollaborator = Collaborator::factory()->make([
        'github_user_id' => 321,
        'github_username' => 'ezrabridger',
    ]);
    $user->collaborators()->save($collaborator);
    $user->collaborators()->save($newCollaborator);

    $user->update(['github_username' => 'kananjarrus']);

    $this->assertEquals('kananjarrus', $collaborator->fresh()->github_username);
    $this->assertEquals('ezrabridger', $newCollaborator->fresh()->github_username);
});

test('collaborator github usernames are only updated when github user id is set', function () {
    $user = User::factory()->create([
        'github_user_id' => null,
        'github_username' => 'calebdume',
    ]);
    $collaborator = Collaborator::factory()->make([
        'github_user_id' => null,
        'github_username' => 'calebdume',
    ]);
    $user->collaborators()->save($collaborator);

    $user->update(['github_username' => 'kananjarrus']);

    $this->assertEquals('calebdume', $collaborator->fresh()->github_username);
});

test('updating collaborator github user id on user update', function () {
    $user = User::factory()->create([
        'github_user_id' => null,
        'github_username' => 'calebdume',
    ]);
    $collaborator = Collaborator::factory()->make([
        'github_user_id' => null,
        'github_username' => 'calebdume',
    ]);
    $user->collaborators()->save($collaborator);

    $user->update(['github_user_id' => 123]);

    $this->assertEquals(123, $collaborator->fresh()->github_user_id);
});

test('collaborator github user ids are only updated where github username matches', function () {
    $user = User::factory()->create([
        'github_user_id' => null,
        'github_username' => 'calebdume',
    ]);
    $collaborator = Collaborator::factory()->make([
        'github_user_id' => null,
        'github_username' => 'calebdume',
    ]);
    $newCollaborator = Collaborator::factory()->make([
        'github_user_id' => null,
        'github_username' => 'ezrabridger',
    ]);
    $user->collaborators()->save($collaborator);
    $user->collaborators()->save($newCollaborator);

    $user->update(['github_user_id' => 123]);

    $this->assertEquals(123, $collaborator->fresh()->github_user_id);
    $this->assertNull($newCollaborator->fresh()->github_user_id);
});

test('collaborator github user id is only updated when it is null', function () {
    $user = User::factory()->create([
        'github_user_id' => null,
        'github_username' => 'calebdume',
    ]);
    $collaborator = Collaborator::factory()->make([
        'github_user_id' => 321,
        'github_username' => 'calebdume',
    ]);
    $user->collaborators()->save($collaborator);

    $user->update(['github_user_id' => 123]);

    $this->assertEquals(321, $collaborator->fresh()->github_user_id);
});
