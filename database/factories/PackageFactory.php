<?php

namespace Database\Factories;

use App\Collaborator;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => fake()->sentence(),
            'url' => fake()->url().'/'.fake()->slug(),
            'picture_url' => 'https://picsum.photos/380/220/?random',
            'description' => markdown('# '.fake()->sentence()."\n\n"
                .implode(' ', fake()->sentences(2))."\n\n"
                .implode(' ', fake()->sentences(4))),
            'instructions' => markdown('```'.implode(' ', fake()->sentences(4)).'```'),
            'author_id' => Collaborator::factory(),
            'composer_name' => fake()->slug().'/'.fake()->slug(),
            'is_disabled' => false,
            'abstract' => fake()->text(190),
            'readme' => markdown('# '.fake()->sentence()."\n\n"
                .implode(' ', fake()->sentences(2))."\n\n"
                .implode(' ', fake()->sentences(4))),
        ];
    }

    public function disabled(): self
    {
        return $this->state(['is_disabled' => true]);
    }
}
