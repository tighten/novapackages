<?php

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('ensures packages feed response code and structure', function () {
    Package::factory(5)->create();

    $response = $this->getJson('/api/packages.json');

    $response
        ->assertStatus(200)
        ->assertJson(function (AssertableJson $json) {
            $json
                ->count(5)
                ->first(function (AssertableJson $json) {
                    $json->hasAll(['name', 'author', 'abstract', 'url', 'tags']);
                });
        });
});
