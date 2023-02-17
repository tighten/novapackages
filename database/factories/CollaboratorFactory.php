<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CollaboratorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->firstName().' '.fake()->lastName(),
            'url' => fake()->url(),
            'description' => implode(' ', fake()->sentences(2))."\n\n".implode(' ', fake()->sentences(2)),
            'github_username' => fake()->slug(),
        ];
    }
}
