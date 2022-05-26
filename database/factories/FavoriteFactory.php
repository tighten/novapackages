<?php

namespace Database\Factories;

use App\Favorite;
use App\Package;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class FavoriteFactory extends Factory
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
        ];
    }
}
