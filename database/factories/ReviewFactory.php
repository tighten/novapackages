<?php

use Faker\Generator as Faker;

$factory->define(App\Review::class, function (Faker $faker) {
    return [
        'user_id' => factory(App\User::class)->create()->id,
        'package_id' => factory(App\Package::class)->create()->id,
        'content' => implode(' ', $faker->sentences(4)) . "\n\n" . implode(' ', $faker->sentences(3)),
    ];
});
