<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        return [
            'title' => fake()->word,
            'author' => fake()->name,
            'description' => fake()->sentence,
            'publisher' => fake()->company,
            'category_id' => Category::factory(),
            'quantity' => $quantity,
            'available' => fake()->numberBetween(1, $quantity), 
            'status' => 1,
        ];
    }
}
