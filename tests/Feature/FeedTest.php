<?php

use App\Models\Package;

test('the recent feed loads', function () {
    Package::factory()->create();

    $response = $this->get('feeds/recent');

    $response->assertSuccessful();
});
