<?php

use App\Models\Package;

test('displays sitemap', function () {
    [$packageA, $packageB] = Package::factory()->count(2)->create();

    $response = $this->get(route('sitemap'));

    $response->assertSuccessful();

    $response->assertSeeText($packageA->composer_name);
    $response->assertSeeText($packageB->composer_name);
});
