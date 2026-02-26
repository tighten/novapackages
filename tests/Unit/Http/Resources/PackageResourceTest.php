<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\PackageResource;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PackageResourceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_abstract_is_returned_if_the_resource_has_an_abstract(): void
    {
        $abstract = 'This is the test abstract';
        $package = Package::factory()->create([
            'abstract' => $abstract,
            'description' => 'This is the test description',
        ]);

        $packageResource = (PackageResource::from($package));

        $this->assertEquals($abstract, $packageResource['abstract']);
    }

    #[Test]
    public function an_abstractified_value_is_returned_when_the_abstract_is_null(): void
    {
        $package = Package::factory()->create([
            'abstract' => null,
        ]);

        $packageResource = (PackageResource::from($package));

        $this->assertNotNull($packageResource['abstract']);
        $this->assertEquals($packageResource['abstract'], $package->abstract);
    }
}
