<?php

namespace Tests\Feature;

use App\Collaborator;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollaboratorClaimTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_claim_a_collaborator()
    {
        $this->withoutEvents();

        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();

        $this->assertNull($collaborator->user_id);

        $response = $this->be($user)->post(route('app.collaborators.claims.store', [$collaborator]));

        $this->assertEquals($user->id, $collaborator->refresh()->user->id);
    }

    /** @test */
    public function users_can_claim_a_second_collaborator()
    {
        $this->withoutEvents();

        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $secondCollaborator = Collaborator::factory()->create();

        $response = $this->be($user)->post(route('app.collaborators.claims.store', [$collaborator]));
        $response = $this->be($user)->post(route('app.collaborators.claims.store', [$secondCollaborator]));

        $this->assertEquals($user->id, $collaborator->refresh()->user_id);
        $this->assertEquals($user->id, $secondCollaborator->refresh()->user_id);
    }
}
