<?php

namespace Tests\Feature;

use App\Collaborator;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function updating_collaborator_names_when_the_user_name_changes()
    {
        $user = factory(User::class)->create([
            'name' => 'Caleb Dume',
            'github_username' => 'calebdume',
        ]);
        $collaborator = factory(Collaborator::class)->make([
            'name' => 'Caleb Dume',
            'github_username' => 'calebdume',
        ]);
        $user->collaborators()->save($collaborator);

        $user->update(['name' => 'Kanan Jarrus']);

        $this->assertEquals('Kanan Jarrus', $collaborator->fresh()->name);
    }

    /** @test */
    function updating_collaborator_names_only_updates_where_same_github_username()
    {
        $user = factory(User::class)->create([
            'name' => 'Caleb Dume',
            'github_username' => 'calebdume',
        ]);
        $collaborator = factory(Collaborator::class)->make([
            'name' => 'Caleb Dume',
            'github_username' => 'calebdume',
        ]);
        $newCollaborator = factory(Collaborator::class)->make([
            'name' => 'Ezra Bridger',
            'github_username' => 'ezrabridger',
        ]);
        $user->collaborators()->save($collaborator);
        $user->collaborators()->save($newCollaborator);

        $user->update(['name' => 'Kanan Jarrus']);

        $this->assertEquals('Kanan Jarrus', $collaborator->fresh()->name);
        $this->assertEquals('Ezra Bridger', $newCollaborator->fresh()->name);
    }

    /** @test */
    function updating_collaborator_github_usernames_when_the_user_github_username_changes()
    {
        $user = factory(User::class)->create([
            'github_user_id' => 123,
            'github_username' => 'calebdume',
        ]);
        $collaborator = factory(Collaborator::class)->make([
            'github_user_id' => 123,
            'github_username' => 'calebdume',
        ]);
        $user->collaborators()->save($collaborator);

        $user->update(['github_username' => 'kananjarrus']);

        $this->assertEquals('kananjarrus', $collaborator->fresh()->github_username);
    }

    /** @test */
    function updating_collaborator_github_usernames_only_updates_where_same_github_user_id()
    {
        $user = factory(User::class)->create([
            'github_user_id' => 123,
            'github_username' => 'calebdume',
        ]);
        $collaborator = factory(Collaborator::class)->make([
            'github_user_id' => 123,
            'github_username' => 'calebdume',
        ]);
        $newCollaborator = factory(Collaborator::class)->make([
            'github_user_id' => 321,
            'github_username' => 'ezrabridger',
        ]);
        $user->collaborators()->save($collaborator);
        $user->collaborators()->save($newCollaborator);

        $user->update(['github_username' => 'kananjarrus']);

        $this->assertEquals('kananjarrus', $collaborator->fresh()->github_username);
        $this->assertEquals('ezrabridger', $newCollaborator->fresh()->github_username);
    }

    /** @test */
    function collaborator_github_usernames_are_only_updated_when_github_user_id_is_set()
    {
        $user = factory(User::class)->create([
            'github_user_id' => null,
            'github_username' => 'calebdume',
        ]);
        $collaborator = factory(Collaborator::class)->make([
            'github_user_id' => null,
            'github_username' => 'calebdume',
        ]);
        $user->collaborators()->save($collaborator);

        $user->update(['github_username' => 'kananjarrus']);

        $this->assertEquals('calebdume', $collaborator->fresh()->github_username);
    }
}
