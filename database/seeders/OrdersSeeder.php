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
        $customerCount = User::where('role', 'customer')->count();
        $productCount = Product::count();

        if ($customerCount === 0 || $productCount === 0) {
            $this->command->warn('Customers or Products are missing. Skipping OrdersSeeder.');
            return;
        }

        Order::factory(30)->create()->each(function ($order) {
            $products = Product::inRandomOrder()->take(rand(1, 5))->get();

            foreach ($products as $product) {
                $order->orderItems()->create([
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5),
                    'price' => $product->price,
                ]);
            }
        });

        $this->command->info('Orders seeded successfully with random items.');
    }
}
