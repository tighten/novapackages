<?php

use App\User;
use Faker\Generator as Faker;

$factory->define(App\Screenshot::class, function (Faker $faker) {
    return [
        'uploader_id' => factory(User::class)->create()->id,
        'path' => 'path/to/screenshot.jpg',
    ];
});
