<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('users with no email must submit an email', function () {
    $user = User::factory()->create([
        'email' => null,
    ]);

    $response = $this->be($user)->followingRedirects()->get(route('app.collaborators.index'));

    expect(url()->current())->toEqual(route('app.email.create'));
});

test('user can submit a email address if the github oauth handshake doesnt return one', function () {
    $updatedEmail = 'john@example.com';
    $user = User::factory()->create([
        'email' => null,
    ]);

    $response = $this->actingAs($user)->post(route('app.email.store'), [
        'email' => $updatedEmail,
    ]);

    $response->assertRedirect(route('home'));
    expect($user->fresh()->email)->toEqual($updatedEmail);
});

test('the email address is required', function () {
    $user = User::factory()->create([
        'email' => null,
    ]);

    $response = $this->actingAs($user)->post(route('app.email.store'), [
        'email' => null,
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('email');
});

test('the email address must be valid', function () {
    $user = User::factory()->create([
        'email' => null,
    ]);

    $response = $this->actingAs($user)->post(route('app.email.store'), [
        'email' => 'not-valid',
    ]);

    $response->assertStatus(302);
    $response->assertSessionHasErrors('email');
});

test('the email address must be unique', function () {
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
});
