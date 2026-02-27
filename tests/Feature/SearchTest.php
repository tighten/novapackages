<?php

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


it('returns matching results', function () {
    $package = Package::factory()->create(['name' => 'Dancing hyenas']);

    $response = $this->get(route('search', ['q' => 'hyenas']));

    $response->assertSee('Dancing hyenas');
});

it('doesnt return non matching results', function () {
    $package = Package::factory()->create(['name' => 'Dancing hyenas']);

    $response = $this->get(route('search', ['q' => 'acrobats']));

    $response->assertDontSee('Dancing hyenas');
});

it('ignores disabled packages', function () {
    $package2 = Package::factory()->disabled()->create(['name' => 'An alligator']);

    $response = $this->get(route('search', ['q' => 'a']));

    $response->assertDontSee('alligator');
});
