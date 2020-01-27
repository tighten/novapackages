<?php

use Faker\Generator as Faker;

$factory->define(App\Collaborator::class, function (Faker $faker) {
    return [
        'name' => $faker->firstName.' '.$faker->lastName,
        'url' => $faker->url,
        'description' => implode(' ', $faker->sentences(2))."\n\n".implode(' ', $faker->sentences(2)),
        'github_username' => $faker->slug,
    ];
});
