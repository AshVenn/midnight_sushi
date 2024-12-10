<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        // Ensure there are users and products to work with
        if (User::count() === 0 || Product::count() === 0) {
            $this->command->warn('Users or Products are missing. Skipping OrdersSeeder.');
            return;
        }

        Order::factory(10)->create()->each(function ($order) {
            // Add random order items
            $products = Product::inRandomOrder()->take(rand(1, 5))->get(); // Ensure at least one product exists

            foreach ($products as $product) {
                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5),
                    'price' => $product->price, // Use the product price at the time of order creation
                ]);
            }
        });
    }
}
