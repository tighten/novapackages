<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Events\NewUserSignedUp;
use App\Http\Remotes\GitHub;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery as m;
use Tests\TestCase;

class NewUserCollaborateCreateAndOwnTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_collaborator_for_new_users()
    {
        Notification::fake();

        $github = m::mock(GitHub::class)->shouldIgnoreMissing();
        $this->app->instance(GitHub::class, $github);

        $user = User::factory()->create();

        event(new NewUserSignedUp($user));

        $this->assertEquals(1, Collaborator::count());
    }

    /** @test */
    public function it_claims_collaborator_for_new_users_if_matching_by_github_username()
    {
        Notification::fake();

        $collaborator = Collaborator::factory()->create([
            'github_username' => 'josecanhelp',
        ]);

        $user = User::factory()->create([
            'github_username' => 'josecanhelp',
        ]);

        event(new NewUserSignedUp($user));

        $this->assertEquals(1, Collaborator::count());
        $this->assertEquals($user->id, $collaborator->refresh()->user_id);
    }
}
