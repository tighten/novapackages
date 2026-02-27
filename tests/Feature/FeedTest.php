<?php

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

test('the recent feed loads', function () {
    Package::factory()->create();

    $response = $this->get('feeds/recent');

    $response->assertSuccessful();
});
