<?php

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


test('the recent feed loads', function () {
    Package::factory()->create();

    $response = $this->get('feeds/recent');

    $response->assertSuccessful();
});
