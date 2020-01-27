<?php

namespace Tests\Feature;

use App\Events\NewUserSignedUp;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
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
        ];

        $this->mockSocialiteWithUserData($fakeUserData);

        $existingUser = factory(User::class)->create([
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
        ];

        $this->mockSocialiteWithUserData($fakeUserData);

        $existingUser = factory(User::class)->create([
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
}
