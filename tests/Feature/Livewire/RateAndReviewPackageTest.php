<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\RateAndReviewPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class RateAndReviewPackageTest extends TestCase
{
    /** @test */
    public function the_component_can_render()
    {
        $component = Livewire::test(RateAndReviewPackage::class);

        $component->assertStatus(200);
    }
}
