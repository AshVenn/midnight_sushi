<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Category;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure we have a Category
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();

        return [
            'name' => $this->faker->words(3, true),    
            'slug' => null,                              
            'price' => $this->faker->randomFloat(2, 5, 50),  
            'discount_price' => null,                      
            'description' => $this->faker->sentence(),
            'allergens' => ['soy', 'fish'],              
            'image' => '/images/midnight_sushi/midnight_product.jpg',
            'is_available' => true,
            'category_id' => $category->id,
        ];
    }
}
