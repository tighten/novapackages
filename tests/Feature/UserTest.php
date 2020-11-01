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
    public function updating_collaborator_names_when_the_user_name_changes()
    {
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
    }

    /** @test */
    public function updating_collaborator_names_only_updates_where_same_github_username()
    {
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
    }

    /** @test */
    public function updating_collaborator_github_usernames_when_the_user_github_username_changes()
    {
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
    }

    /** @test */
    public function updating_collaborator_github_usernames_only_updates_where_same_github_user_id()
    {
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
    }

    /** @test */
    public function collaborator_github_usernames_are_only_updated_when_github_user_id_is_set()
    {
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
    }

    /** @test */
    public function updating_collaborator_github_user_id_on_user_update()
    {
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
    }

    /** @test */
    public function collaborator_github_user_ids_are_only_updated_where_github_username_matches()
    {
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
    }

    /** @test */
    public function collaborator_github_user_id_is_only_updated_when_it_is_null()
    {
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
    }
}
