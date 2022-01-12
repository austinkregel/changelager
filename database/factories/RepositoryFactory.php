<?php

namespace Database\Factories;

use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class RepositoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'team_id' => Team::factory(),
            'name' => $this->faker->word,
            'slug' => $this->faker->slug,
            'url' => $this->faker->url,
            'is_public' => $this->faker->boolean,
            'use_v_in_version' => $this->faker->boolean,
            'public_key' => $this->faker->sha256,
            'private_key' => $this->faker->sha256,
            'last_released_at' => $this->faker->dateTime,
            'last_released_version' => sprintf('%s.%s.%s', random_int(0, 9), random_int(0, 9), random_int(0, 9)),
        ];
    }
}
