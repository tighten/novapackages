<?php

namespace Tests\Feature;

use App\Collaborator;
use App\Events\CollaboratorCreated;
use App\Http\Remotes\GitHub;
use App\Package;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Mockery as m;
use Tests\TestCase;

class CollaboratorCrudTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Notification::fake();
    }

    /** @test */
    public function users_can_create_collaborators()
    {
        Event::fake();

        $github = m::mock(GitHub::class)->shouldIgnoreMissing();
        $this->app->instance(GitHub::class, $github);

        $user = User::factory()->create();

        $this->be($user)->post(route('app.collaborators.store'), [
            'name' => 'Matt Stauffer',
            'github_username' => 'mattstauffer',
            'url' => 'https://mattstauffer.com/',
        ]);

        Event::assertDispatched(CollaboratorCreated::class);
        $this->assertEquals(1, Collaborator::count());
    }

    /** @test */
    public function only_one_collaborator_is_allowed_per_github_username()
    {
        $github = m::mock(GitHub::class)->shouldIgnoreMissing();
        $this->app->instance(GitHub::class, $github);

        $user = User::factory()->create();

        $this->be($user)->post(route('app.collaborators.store'), [
            'name' => 'Matt Stauffer',
            'github_username' => 'mattstauffer',
            'url' => 'https://mattstauffer.com/',
        ]);

        $this->be($user)->post(route('app.collaborators.store'), [
            'name' => 'Stat Shmauffer',
            'github_username' => 'mattstauffer',
            'url' => 'https://statshmauffer.com/',
        ]);

        $this->assertEquals(1, Collaborator::count());
    }

    /** @test */
    public function collaborators_receive_avatar_url_from_github_upon_creation()
    {
        $github = m::mock(GitHub::class);
        $github->shouldReceive('user')
            ->with('not_a_real_github_username')
            ->andReturn(['avatar_url' => 'http://www.image.com/']);

        $this->app->instance(GitHub::class, $github);

        $user = User::factory()->create();

        $this->be($user)->post(route('app.collaborators.store'), [
            'name' => 'Matt Stauffer',
            'github_username' => 'not_a_real_github_username',
            'url' => 'https://mattstauffer.com/',
        ]);

        $this->assertEquals('http://www.image.com/', Collaborator::first()->avatar);
    }
}
