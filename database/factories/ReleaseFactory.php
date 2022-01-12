<?php

namespace Database\Factories;

use App\Models\Repository;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReleaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'repository_id' => Repository::factory(),
            'version' => sprintf('%s.%s.%s', random_int(0, 9), random_int(0, 9), random_int(0, 9)),
            'notes' => $this->faker->text,
            'hash' => $this->faker->md5,
        ];
    }
}
