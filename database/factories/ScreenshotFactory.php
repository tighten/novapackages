<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;

class ScreenshotFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'uploader_id' => User::factory(),
            'path' => function () {
                return $this->generatePlaceholderImage();
            },
        ];
    }

    private function generatePlaceholderImage(): string
    {
        $width = $this->faker->randomElement([800, 1024, 1280]);
        $height = $this->faker->randomElement([450, 600, 720]);
        $bg = $this->faker->hexColor();
        $text = urlencode($this->faker->words(2, true));

        $filename = 'screenshots/' . $this->faker->uuid() . '.svg';

        $svg = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" width="{$width}" height="{$height}">
            <rect width="100%" height="100%" fill="{$bg}"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="white" font-size="32" font-family="sans-serif">{$text}</text>
        </svg>
        SVG;

        Storage::disk('public')->put($filename, $svg);

        return $filename;
    }
}
