<?php

use App\Http\Remotes\GitHub;
use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Mockery as m;

test('the url field is optional', function () {
    Event::fake();

    $github = m::mock(GitHub::class)->shouldIgnoreMissing();
    app()->instance(GitHub::class, $github);

    $user = User::factory()->create();
    $userData = [
        'name' => 'John Smith',
        'url' => '',
        'description' => 'This is a description for John',
        'github_username' => 'johnsmith',
    ];

    $response = $this->actingAs($user)->post(route('app.collaborators.store'), $userData);

    $response->assertSessionHasNoErrors();
    expect(Collaborator::all())->toHaveCount(1);

    $collaborator = Collaborator::first();

    expect($collaborator->name)->toEqual($userData['name']);
    expect($collaborator->url)->toEqual($userData['url']);
    expect($collaborator->description)->toEqual($userData['description']);
    expect($collaborator->github_username)->toEqual($userData['github_username']);
});
