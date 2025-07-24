<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->words(3, true);
        return [
            'name'           => fake()->words(3, true),
            'description'    => fake()->sentence(),
            'image'          => "https://robohash.org/{$name}.png",
            'last_active_at' => fake()->dateTime(),
        ];
    }
}
