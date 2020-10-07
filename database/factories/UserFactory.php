<?php

use App\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'remember_token' => Str::random(10),
        'github_username' => $faker->userName,
    ];
});

$factory->state(User::class, 'admin', function ($faker) use ($factory) {
    return array_merge($factory->raw(User::class), [
        'role' => User::ADMIN_ROLE,
    ]);
});
