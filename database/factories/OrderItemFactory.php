<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Order;
use App\Models\Product;
use App\Models\Menu;
use App\Models\OrderItem;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Ensure we have an Order, a Product, and a Menu to associate
        $order = Order::inRandomOrder()->first() ?? Order::factory()->create();
        $isProduct = rand(0, 1) === 1;
        
        if ($isProduct) {
            $orderable = Product::inRandomOrder()->first() ?? Product::factory()->create();
            $orderableType = 'Product'; 
        } else {
            $orderable = Menu::inRandomOrder()->first() ?? Menu::factory()->create();
            $orderableType = 'Menu'; 
        }

        return [
            'order_id'       => $order->id,
            'orderable_type' => $orderableType,
            'orderable_id'   => $orderable->id,
            'quantity'       => $this->faker->numberBetween(1, 10),
            'price'          => $orderable->price,
        ];
    }
}
