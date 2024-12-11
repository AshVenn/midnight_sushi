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
        // Ensure there are customers, delivery persons, and products to work with
        $customerCount = User::where('role', 'customer')->count();
        $deliveryPersonCount = User::where('role', 'delivery_person')->count();
        $productCount = Product::count();

        if ($customerCount === 0 || $productCount === 0) {
            $this->command->warn('Customers or Products are missing. Skipping OrdersSeeder.');
            return;
        }

        // Create 10 orders
        Order::factory(10)->create()->each(function ($order) {
            // Add random order items
            $products = Product::inRandomOrder()->take(rand(1, 5))->get();

            foreach ($products as $product) {
                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5),
                    'price' => $product->price, // Use the product price at the time of order creation
                ]);
            }
        });

        $this->command->info('Orders seeded successfully with random items.');
    }
}
