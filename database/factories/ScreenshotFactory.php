<?php

namespace Database\Factories;

use App\Screenshot;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScreenshotFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Screenshot::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uploader_id' => User::factory(),
            'path' => 'path/to/screenshot.jpg',
        ];
    }
}
