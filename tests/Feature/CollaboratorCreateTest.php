<?php

namespace Tests\Feature;

use App\Http\Remotes\GitHub;
use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        $user = User::factory()->create();
        $userData = [
            'name' => 'John Smith',
            'url' => '',
            'description' => 'This is a description for John',
            'github_username' => 'johnsmith',
        ];

        $response = $this->actingAs($user)->post(route('app.collaborators.store'), $userData);

        $response->assertSessionHasNoErrors();
        $this->assertCount(1, Collaborator::all());

        $collaborator = Collaborator::first();

        $this->assertEquals($userData['name'], $collaborator->name);
        $this->assertEquals($userData['url'], $collaborator->url);
        $this->assertEquals($userData['description'], $collaborator->description);
        $this->assertEquals($userData['github_username'], $collaborator->github_username);
    }
}
