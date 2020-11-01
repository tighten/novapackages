<?php

namespace Database\Factories;

use App\Package;
use App\Review;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

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
            'content' => implode(' ', $this->faker->sentences(4)) . "\n\n" . implode(' ', $this->faker->sentences(3)),
        ];
    }
}
