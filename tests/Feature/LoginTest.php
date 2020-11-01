<?php

namespace Tests\Feature;

use App\Events\NewUserSignedUp;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_login_after_socialite_authentication()
    {
        Event::fake();

        $fakeUserData = [
            'name' => 'test',
            'email' => 'test@example.com',
            'avatar' => 'http://someimg.jpg',
            'github_username' => 'test',
            'github_user_id' => 123,
        ];

        $this->mockSocialiteWithUserData($fakeUserData);

        $response = $this->get('login/github/callback');

        $response->assertRedirect(route('home'));
        Event::assertDispatched(NewUserSignedUp::class);
        $this->assertCount(1, User::all());
        $user = User::first();
        $this->assertArraySubset($fakeUserData, $user->toArray());
        $this->assertEquals($user->id, auth()->id());
    }

    /** @test */
    public function a_user_can_login_after_socialite_authentication_when_the_socialite_response_is_missing_an_email()
    {
        Event::fake();

        $fakeUserData = [
            'name' => 'test',
            'email' => null,
            'avatar' => 'http://someimg.jpg',
            'github_username' => 'test',
            'github_user_id' => 123,
        ];

        $this->mockSocialiteWithUserData($fakeUserData);

        $response = $this->get('login/github/callback');

        $response->assertRedirect(route('home'));
        Event::assertDispatched(NewUserSignedUp::class);
        $this->assertCount(1, User::all());
        $user = User::first();
        $this->assertArraySubset($fakeUserData, $user->toArray());
        $this->assertEquals($user->id, auth()->id());
    }

    /** @test */
    public function a_user_is_updated_if_the_email_matches_the_socialite_response()
    {
        Event::fake();

        $fakeUserData = [
            'name' => 'new name',
            'email' => 'test@example.com',
            'avatar' => 'http://new-test-avatar.jpg',
            'github_username' => 'newgithubname',
            'github_user_id' => 123,
        ];

        $this->mockSocialiteWithUserData($fakeUserData);

        $existingUser = User::factory()->create([
            'name' => 'John Smith',
            'email' => 'test@example.com',
            'avatar' => 'http://test-avatar.jpg',
            'github_username' => 'test',
        ]);

        $response = $this->get('login/github/callback');

        $response->assertRedirect(route('home'));
        Event::assertNotDispatched(NewUserSignedUp::class);
        $this->assertCount(1, User::all());
        $existingUser->refresh();
        $this->assertArraySubset($fakeUserData, $existingUser->toArray());
        $this->assertEquals($existingUser->id, auth()->id());
    }

    /** @test */
    public function a_user_is_updated_if_the_github_username_matches_the_socialite_response()
    {
        Event::fake();

        $fakeUserData = [
            'name' => 'new name',
            'email' => 'newtest@example.com',
            'avatar' => 'http://new-test-avatar.jpg',
            'github_username' => 'githubname',
            'github_user_id' => 123,
        ];

        $this->mockSocialiteWithUserData($fakeUserData);

        $existingUser = User::factory()->create([
            'name' => 'John Smith',
            'email' => 'test@example.com',
            'avatar' => 'http://test-avatar.jpg',
            'github_username' => 'githubname',
        ]);

        $response = $this->get('login/github/callback');

        $response->assertRedirect(route('home'));
        Event::assertNotDispatched(NewUserSignedUp::class);
        $this->assertCount(1, User::all());
        $existingUser->refresh();
        $this->assertArraySubset($fakeUserData, $existingUser->toArray());
        $this->assertEquals($existingUser->id, auth()->id());
    }

    /** @test */
    public function the_github_user_id_is_updated_if_it_is_null()
    {
        Event::fake();

        $fakeUserData = [
            'name' => 'Ahsoka Tano',
            'email' => 'ahsoka@example.com',
            'avatar' => 'http://new-test-avatar.jpg',
            'github_username' => 'ahsokatano',
            'github_user_id' => 123,
        ];

        $this->mockSocialiteWithUserData($fakeUserData);

        $existingUser = User::factory()->create([
            'name' => 'Ahsoka Tano',
            'email' => 'ahsoka@example.com',
            'avatar' => 'http://test-avatar.jpg',
            'github_username' => 'ahsokatano',
            'github_user_id' => null,
        ]);

        $response = $this->get('login/github/callback');

        $response->assertRedirect(route('home'));
        Event::assertNotDispatched(NewUserSignedUp::class);
        $this->assertCount(1, User::all());
        $this->assertEquals(123, $existingUser->fresh()->github_user_id);
    }
}
