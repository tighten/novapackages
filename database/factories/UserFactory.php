<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'remember_token' => Str::random(10),
            'github_username' => fake()->userName(),
        ];
    }

    public function admin(): self
    {
        return $this->state(['role' => User::ADMIN_ROLE]);
    }
}
