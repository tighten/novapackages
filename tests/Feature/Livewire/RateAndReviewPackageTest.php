<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\RateAndReviewPackage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RateAndReviewPackageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cannot_rate_a_package()
    {
        $this->markTestIncomplete();


    }

    /** @test */
    public function users_cannot_rate_their_own_package()
    {
        $this->markTestIncomplete();


    }
}
