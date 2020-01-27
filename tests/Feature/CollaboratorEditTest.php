<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Http\Remotes\GitHub;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery as m;
use Tests\TestCase;

class CollaboratorEditTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_not_view_the_edit_collaborator_form_for_a_collaborator_that_is_not_assigned_to_them()
    {
        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $collaborator = factory(Collaborator::class)->make();
        $userB->collaborators()->save($collaborator);

        $response = $this->actingAs($userA)->get(route('app.collaborators.edit', $collaborator));

        $response->assertStatus(403);
    }

    /** @test */
    public function a_user_can_view_the_edit_collaborator_form()
    {
        $user = factory(User::class)->create();
        $collaborator = factory(Collaborator::class)->make();
        $user->collaborators()->save($collaborator);

        $response = $this->actingAs($user)->get(route('app.collaborators.edit', $collaborator));

        $response->assertSuccessful();
        $response->assertViewHas('collaborator', function ($viewCollaborator) use ($collaborator) {
            return $viewCollaborator->is($collaborator);
        });
    }

    /** @test */
    public function a_user_can_not_update_a_collaborator_that_is_not_assigned_to_them()
    {
        $github = m::mock(GitHub::class)->shouldIgnoreMissing();
        $this->app->instance(GitHub::class, $github);

        $userA = factory(User::class)->create();
        $userB = factory(User::class)->create();
        $collaboratorAttributes = [
            'name' => 'John Smith',
            'url' => 'http://johnsplace.com',
            'description' => 'This is a description for John',
            'github_username' => 'johnsmith',
        ];
        $collaborator = factory(Collaborator::class)->make($collaboratorAttributes);
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
    }

    /** @test */
    public function a_user_can_update_a_collaborator_assigned_to_them()
    {
        $github = m::mock(GitHub::class)->shouldIgnoreMissing();
        $this->app->instance(GitHub::class, $github);

        $user = factory(User::class)->create();
        $collaborator = factory(Collaborator::class)->make([
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

        $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), $userData);

        tap($collaborator->fresh(), function ($collaborator) use ($userData) {
            $this->assertEquals($userData['name'], $collaborator->name);
            $this->assertEquals($userData['url'], $collaborator->url);
            $this->assertEquals($userData['description'], $collaborator->description);
            $this->assertEquals($userData['github_username'], $collaborator->github_username);
        });
    }

    /** @test */
    public function the_name_field_is_required()
    {
        $user = factory(User::class)->create();
        $collaboratorAttributes = [
            'name' => 'John Smith',
            'url' => 'http://johnsplace.com',
            'description' => 'This is a description for John',
            'github_username' => 'johnsmith',
        ];
        $collaborator = factory(Collaborator::class)->make($collaboratorAttributes);
        $user->collaborators()->save($collaborator);

        $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
            'name' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('name');
        $this->assertEquals($collaboratorAttributes['name'], $collaborator->fresh()->name);
    }

    /** @test */
    public function the_url_field_is_optional()
    {
        $user = factory(User::class)->create();
        $collaboratorAttributes = [
            'name' => 'John Smith',
            'url' => 'http://johnsplace.com',
            'description' => 'This is a description for John',
            'github_username' => 'johnsmith',
        ];
        $collaborator = factory(Collaborator::class)->make($collaboratorAttributes);
        $user->collaborators()->save($collaborator);

        $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
            'name' => 'John Smith',
            'url' => '',
            'description' => 'This is a description for John',
            'github_username' => 'johnsmith',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertNull($collaborator->fresh()->url);
    }

    /** @test */
    public function the_url_field_must_be_valid()
    {
        $user = factory(User::class)->create();
        $collaboratorAttributes = [
            'name' => 'John Smith',
            'url' => 'http://johnsplace.com',
            'description' => 'This is a description for John',
            'github_username' => 'johnsmith',
        ];
        $collaborator = factory(Collaborator::class)->make($collaboratorAttributes);
        $user->collaborators()->save($collaborator);

        $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
            'url' => 'not-valid',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('url');
        $this->assertEquals($collaboratorAttributes['url'], $collaborator->fresh()->url);
    }

    /** @test */
    public function the_github_username_field_is_required()
    {
        $user = factory(User::class)->create();
        $collaboratorAttributes = [
            'name' => 'John Smith',
            'url' => 'http://johnsplace.com',
            'description' => 'This is a description for John',
            'github_username' => 'johnsmith',
        ];
        $collaborator = factory(Collaborator::class)->make($collaboratorAttributes);
        $user->collaborators()->save($collaborator);

        $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
            'github_username' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('github_username');
        $this->assertEquals($collaboratorAttributes['github_username'], $collaborator->fresh()->github_username);
    }

    /** @test */
    public function the_github_username_can_be_unchanged()
    {
        $user = factory(User::class)->create();
        $collaboratorAttributes = [
            'name' => 'John Smith',
            'url' => 'http://johnsplace.com',
            'description' => 'This is a description for John',
            'github_username' => 'johnsmith',
        ];
        $collaborator = factory(Collaborator::class)->make($collaboratorAttributes);
        $user->collaborators()->save($collaborator);

        $response = $this->actingAs($user)->patch(route('app.collaborators.update', $collaborator), [
            'github_username' => $collaboratorAttributes['github_username'],
        ]);

        $response->assertStatus(302);
        $this->assertEquals($collaboratorAttributes['github_username'], $collaborator->fresh()->github_username);
    }
}
