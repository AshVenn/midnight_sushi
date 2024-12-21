<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\MenuProduct;
use App\Models\Menu;
use App\Models\Product;

class MenuProductFactory extends Factory
{
    protected $model = MenuProduct::class;

    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),    // or Menu::inRandomOrder()->first()->id
            'product_id' => Product::factory(),
            'quantity' => $this->faker->numberBetween(1,3),
        ];
    }
}
