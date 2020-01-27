<?php

use Faker\Generator as Faker;

$factory->define(App\Package::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence,
        'url' => "{$faker->url}/{$faker->slug}",
        'picture_url' => 'https://picsum.photos/380/220/?random',
        'description' => markdown('# '.$faker->sentence()."\n\n"
            .implode(' ', $faker->sentences(2))."\n\n"
            .implode(' ', $faker->sentences(4))),
        'instructions' => markdown('```'.implode(' ', $faker->sentences(4)).'```'),
        'author_id' => factory(App\Collaborator::class)->create(),
        'composer_name' => $faker->slug.'/'.$faker->slug,
        'is_disabled' => false,
        'abstract' => $faker->text(190),
        'readme' => markdown('# '.$faker->sentence."\n\n"
            .implode(' ', $faker->sentences(2))."\n\n"
            .implode(' ', $faker->sentences(4))),
    ];
});

$factory->state(App\Package::class, 'disabled', [
    'is_disabled' => true,
]);
