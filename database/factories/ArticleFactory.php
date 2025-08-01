<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title'      => fake()->sentence(),
            'content'    => fake()->paragraph(),
            'created_at' => fake()->dateTimeBetween('-1 year'),
        ];
    }
}
