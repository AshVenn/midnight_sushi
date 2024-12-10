<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true), // e.g., "Dragon Roll Sushi"
            'price' => $this->faker->randomFloat(2, 5, 50), // e.g., 5.00 to 50.00
            'description' => $this->faker->sentence(),
            'allergens' => json_encode(['soy', 'fish']), // Example allergens
            'image' => $this->faker->imageUrl(640, 480, 'food', true, 'sushi'),
            'category_id' => Category::inRandomOrder()->first()->id,
        ];
    }
}
