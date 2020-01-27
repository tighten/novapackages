<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Http\Remotes\GitHub;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery as m;
use Tests\TestCase;

class CollaboratorCreateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function the_url_field_is_optional()
    {
        $this->withoutEvents();

        $github = m::mock(GitHub::class)->shouldIgnoreMissing();
        $this->app->instance(GitHub::class, $github);

        $user = factory(User::class)->create();
        $userData = [
            'name' => 'John Smith',
            'url' => '',
            'description' => 'This is a description for John',
            'github_username' => 'johnsmith',
        ];

        $response = $this->actingAs($user)->post(route('app.collaborators.store'), $userData);

        $response->assertSessionHasNoErrors();
        $this->assertCount(1, Collaborator::all());

        tap(Collaborator::first(), function ($collaborator) use ($userData) {
            $this->assertEquals($userData['name'], $collaborator->name);
            $this->assertEquals($userData['url'], $collaborator->url);
            $this->assertEquals($userData['description'], $collaborator->description);
            $this->assertEquals($userData['github_username'], $collaborator->github_username);
        });
    }
}
