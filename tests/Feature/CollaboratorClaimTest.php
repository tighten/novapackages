<?php

namespace Tests\Feature;

use App\Models\Collaborator;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CollaboratorClaimTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_claim_a_collaborator(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();

        $this->assertNull($collaborator->user_id);

        $response = $this->be($user)->post(route('app.collaborators.claims.store', [$collaborator]));

        $this->assertEquals($user->id, $collaborator->refresh()->user->id);
    }

    #[Test]
    public function users_can_claim_a_second_collaborator(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $collaborator = Collaborator::factory()->create();
        $secondCollaborator = Collaborator::factory()->create();

        $response = $this->be($user)->post(route('app.collaborators.claims.store', [$collaborator]));
        $response = $this->be($user)->post(route('app.collaborators.claims.store', [$secondCollaborator]));

        $this->assertEquals($user->id, $collaborator->refresh()->user_id);
        $this->assertEquals($user->id, $secondCollaborator->refresh()->user_id);
    }
}
