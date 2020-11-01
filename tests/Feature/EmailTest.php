<?php

namespace Tests\Feature;

use App\Collaborator;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function users_with_no_email_must_submit_an_email()
    {
        $user = User::factory()->create([
            'email' => null,
        ]);

        $response = $this->be($user)->followingRedirects()->get(route('app.collaborators.index'));

        $this->assertEquals(route('app.email.create'), url()->current());
    }

    /** @test */
    public function user_can_submit_a_email_address_if_the_github_oauth_handshake_doesnt_return_one()
    {
        $updatedEmail = 'john@example.com';
        $user = User::factory()->create([
            'email' => null,
        ]);

        $response = $this->actingAs($user)->post(route('app.email.store'), [
            'email' => $updatedEmail,
        ]);

        $response->assertRedirect(route('home'));
        $this->assertEquals($updatedEmail, $user->fresh()->email);
    }

    /** @test */
    public function the_email_address_is_required()
    {
        $user = User::factory()->create([
            'email' => null,
        ]);

        $response = $this->actingAs($user)->post(route('app.email.store'), [
            'email' => null,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function the_email_address_must_be_valid()
    {
        $user = User::factory()->create([
            'email' => null,
        ]);

        $response = $this->actingAs($user)->post(route('app.email.store'), [
            'email' => 'not-valid',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function the_email_address_must_be_unique()
    {
        $existingEmail = 'john@example.com';
        User::factory()->create(['email' => $existingEmail]);
        $user = User::factory()->create([
            'email' => null,
        ]);

        $response = $this->actingAs($user)->post(route('app.email.store'), [
            'email' => $existingEmail,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }
}
