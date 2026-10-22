<?php

namespace Database\Factories;

use App\Models\Circular;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Circular>
 */
class CircularFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $id = fake()->unique()->numberBetween(1, 999999);

        return [
            'wp_post_id' => $id,
            'title' => 'Circolare n. '.fake()->numberBetween(1, 300).' - '.fake()->sentence(4),
            'url' => 'https://www.icbaseggio.edu.it/circolare-'.$id.'/',
            'published_at' => fake()->dateTimeBetween('-1 month'),
        ];
    }
}
