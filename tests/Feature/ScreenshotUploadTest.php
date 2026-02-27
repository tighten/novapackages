<?php

use App\Models\Screenshot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('authenticated users can upload a screenshot', function () {
    Storage::fake();

    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $response = $this->actingAs($userB)->json('POST', route('app.screenshot-uploads.store'), [
        'screenshot' => File::image('screenshot.jpg'),
    ], [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertStatus(201);
    expect(Screenshot::all())->toHaveCount(1);
    Storage::assertExists($response->json()['path']);
    expect(Screenshot::first()->uploader->id)->toEqual($userB->id);
    $response->assertJsonStructure([
        'id',
        'public_url',
    ]);
});

test('guest users can not upload screenshot', function () {
    Storage::fake();

    $response = $this->json('POST', route('app.screenshot-uploads.store'), [
        'screenshot' => File::image('screenshot.jpg'),
    ], [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertStatus(401);
    expect(Screenshot::all())->toHaveCount(0);
});

test('the uploaded screenshot must be smaller than 2mb', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->json('POST', route('app.screenshot-uploads.store'), [
        'screenshot' => File::create('screenshot.jpg', 2049),
    ], [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('screenshot');
    expect(Screenshot::all())->toHaveCount(0);
});

test('the upload screenshot must be an image', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->json('POST', route('app.screenshot-uploads.store'), [
        'screenshot' => File::create('invlaid-image.pdf'),
    ], [
        'X-Requested-With' => 'XMLHttpRequest',
    ]);

    $response->assertStatus(422);
    $response->assertJsonValidationErrors('screenshot');
    expect(Screenshot::all())->toHaveCount(0);
});
