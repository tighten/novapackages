<?php

use App\Http\Remotes\GitHub;
use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('a user can not view the edit collaborator form for a collaborator that is not assigned to them', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $collaborator = Collaborator::factory()->make();
    $userB->collaborators()->save($collaborator);

    $response = $this->actingAs($userA)->get(route('app.collaborators.edit', $collaborator));

    $response->assertStatus(403);
});

test('a user can view the edit collaborator form', function () {
    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->make();
    $user->collaborators()->save($collaborator);

    $response = $this->actingAs($user)->get(route('app.collaborators.edit', $collaborator));

    $response->assertSuccessful();
    $response->assertViewHas('collaborator', function ($viewCollaborator) use ($collaborator) {
        return $viewCollaborator->is($collaborator);
    });
});

test('a user can not update a collaborator that is not assigned to them', function () {
    $github = m::mock(GitHub::class)->shouldIgnoreMissing();
    app()->instance(GitHub::class, $github);

    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $collaboratorAttributes = [
        'name' => 'John Smith',
        'url' => 'http://johnsplace.com',
        'description' => 'This is a description for John',
        'github_username' => 'johnsmith',
    ];
    $collaborator = Collaborator::factory()->make($collaboratorAttributes);
    $userB->collaborators()->save($collaborator);

    $response = $this->actingAs($userA)->patch(route('app.collaborators.update', $collaborator), [
        'name' => 'Jim Smith',
        'url' => 'http://jimsplace.com/',
        'description' => 'This is a description for Jim',
        'github_username' => 'jimsmith',
    ]);

    $response->assertStatus(403);

    tap($collaborator->fresh(), function ($collaborator) use ($collaboratorAttributes) {
        $this->assertEquals($collaboratorAttributes['name'], $collaborator->name);
        $this->assertEquals($collaboratorAttributes['url'], $collaborator->url);
        $this->assertEquals($collaboratorAttributes['description'], $collaborator->description);
        $this->assertEquals($collaboratorAttributes['github_username'], $collaborator->github_username);
    });
});

test('a user can update a collaborator assigned to them', function () {
    $github = m::mock(GitHub::class)->shouldIgnoreMissing();
    app()->instance(GitHub::class, $github);

    $user = User::factory()->create();
    $collaborator = Collaborator::factory()->make([
        'name' => 'John Smith',
        'url' => 'http://johnsplace.com',
        'description' => 'This is a description for John',
        'github_username' => 'johnsmith',
    ]);
    $user->collaborators()->save($collaborator);
    $userData = [
        'name' => 'Jim Smith',
        'url' => 'http://jimsplace.com/',
        'description' => 'This is a description for Jim',
        'github_username' => 'jimsmith',
    ];

    $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), $userData);
    $response->assertSessionDoesntHaveErrors();

    $collaborator->refresh();

    $this->assertEquals($userData['name'], $collaborator->name);
    $this->assertEquals($userData['url'], $collaborator->url);
    $this->assertEquals($userData['description'], $collaborator->description);
    $this->assertEquals($userData['github_username'], $collaborator->github_username);
});

test('the name field is required', function () {
    $user = User::factory()->create();
    $collaboratorAttributes = [
        'name' => 'John Smith',
        'url' => 'http://johnsplace.com',
        'description' => 'This is a description for John',
        'github_username' => 'johnsmith',
    ];
    $collaborator = Collaborator::factory()->make($collaboratorAttributes);
    $user->collaborators()->save($collaborator);

    $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
        'name' => '',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('name');
    $this->assertEquals($collaboratorAttributes['name'], $collaborator->fresh()->name);
});

test('the url field is optional', function () {
    $user = User::factory()->create();
    $collaboratorAttributes = [
        'name' => 'John Smith',
        'url' => 'http://johnsplace.com',
        'description' => 'This is a description for John',
        'github_username' => 'johnsmith',
    ];
    $collaborator = Collaborator::factory()->make($collaboratorAttributes);
    $user->collaborators()->save($collaborator);

    $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
        'name' => 'John Smith',
        'url' => '',
        'description' => 'This is a description for John',
        'github_username' => 'johnsmith',
    ]);

    $response->assertSessionHasNoErrors();
    $this->assertNull($collaborator->fresh()->url);
});

test('the url field must be valid', function () {
    $user = User::factory()->create();
    $collaboratorAttributes = [
        'name' => 'John Smith',
        'url' => 'http://johnsplace.com',
        'description' => 'This is a description for John',
        'github_username' => 'johnsmith',
    ];
    $collaborator = Collaborator::factory()->make($collaboratorAttributes);
    $user->collaborators()->save($collaborator);

    $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
        'url' => 'not-valid',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('url');
    $this->assertEquals($collaboratorAttributes['url'], $collaborator->fresh()->url);
});

test('the github username field is required', function () {
    $user = User::factory()->create();
    $collaboratorAttributes = [
        'name' => 'John Smith',
        'url' => 'http://johnsplace.com',
        'description' => 'This is a description for John',
        'github_username' => 'johnsmith',
    ];
    $collaborator = Collaborator::factory()->make($collaboratorAttributes);
    $user->collaborators()->save($collaborator);

    $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
        'github_username' => '',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('github_username');
    $this->assertEquals($collaboratorAttributes['github_username'], $collaborator->fresh()->github_username);
});

test('the github username can be unchanged', function () {
    $user = User::factory()->create();
    $collaboratorAttributes = [
        'name' => 'John Smith',
        'url' => 'http://johnsplace.com',
        'description' => 'This is a description for John',
        'github_username' => 'johnsmith',
    ];
    $collaborator = Collaborator::factory()->make($collaboratorAttributes);
    $user->collaborators()->save($collaborator);

    $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
        'github_username' => $collaboratorAttributes['github_username'],
    ]);

    $response->assertStatus(302);
    $this->assertEquals($collaboratorAttributes['github_username'], $collaborator->fresh()->github_username);
});
