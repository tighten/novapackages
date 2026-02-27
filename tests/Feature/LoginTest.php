<?php

use App\Events\NewUserSignedUp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;


test('a user can login after socialite authentication', function () {
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
    expect(User::all())->toHaveCount(1);
    $user = User::first();
    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys($fakeUserData, $user->toArray(), array_keys($fakeUserData));
    expect(auth()->id())->toEqual($user->id);
});

test('a user can login after socialite authentication when the socialite response is missing an email', function () {
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
    expect(User::all())->toHaveCount(1);
    $user = User::first();
    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys($fakeUserData, $user->toArray(), array_keys($fakeUserData));
    expect(auth()->id())->toEqual($user->id);
});

test('a user is updated if the email matches the socialite response', function () {
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
    expect(User::all())->toHaveCount(1);
    $existingUser->refresh();
    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys($fakeUserData, $existingUser->toArray(), array_keys($fakeUserData));
    expect(auth()->id())->toEqual($existingUser->id);
});

test('a user is updated if the github username matches the socialite response', function () {
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
    expect(User::all())->toHaveCount(1);
    $existingUser->refresh();
    $this->assertArrayIsEqualToArrayOnlyConsideringListOfKeys($fakeUserData, $existingUser->toArray(), array_keys($fakeUserData));
    expect(auth()->id())->toEqual($existingUser->id);
});

test('the github user id is updated if it is null', function () {
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
    expect(User::all())->toHaveCount(1);
    expect($existingUser->fresh()->github_user_id)->toEqual(123);
});
