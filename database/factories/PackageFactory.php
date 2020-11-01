<?php

namespace Database\Factories;

use App\Collaborator;
use App\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'url' => "{$this->faker->url}/{$this->faker->slug}",
            'picture_url' => 'https://picsum.photos/380/220/?random',
            'description' => markdown('# '.$this->faker->sentence()."\n\n"
                .implode(' ', $this->faker->sentences(2))."\n\n"
                .implode(' ', $this->faker->sentences(4))),
            'instructions' => markdown('```'.implode(' ', $this->faker->sentences(4)).'```'),
            'author_id' => Collaborator::factory(),
            'composer_name' => $this->faker->slug.'/'.$this->faker->slug,
            'is_disabled' => false,
            'abstract' => $this->faker->text(190),
            'readme' => markdown('# '.$this->faker->sentence."\n\n"
                .implode(' ', $this->faker->sentences(2))."\n\n"
                .implode(' ', $this->faker->sentences(4))),
        ];
    }

    public function disabled(): self
    {
        return $this->state(['is_disabled' => true]);
    }
}
