<?php

namespace Database\Factories;

use App\Screenshot;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScreenshotFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uploader_id' => User::factory(),
            'path' => 'path/to/screenshot.jpg',
        ];
    }
}
