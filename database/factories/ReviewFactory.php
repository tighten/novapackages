<?php

namespace Database\Factories;

use App\Package;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'package_id' => Package::factory(),
            'content' => implode(' ', fake()->sentences(4))."\n\n".implode(' ', fake()->sentences(3)),
        ];
    }
}
