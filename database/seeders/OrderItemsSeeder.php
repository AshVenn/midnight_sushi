<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;

class OrderItemsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        $products = Product::all();

        if ($orders->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No Orders or Products found. Skipping OrderItemsSeeder.');
            return;
        }

        foreach ($orders as $order) {
            $itemsCount = rand(1, 5);
            for ($i = 0; $i < $itemsCount; $i++) {
                $product = $products->random();

                // Create a new OrderItem using the polymorphic columns
                OrderItem::create([
                    'order_id'       => $order->id,
                    'orderable_type' => \App\Models\Product::class,
                    'orderable_id'   => $product->id,
                    'quantity'       => rand(1, 3),
                    'price'          => $product->price,
                ]);
            }
        }

        $this->command->info('OrderItems seeded successfully (polymorphic items added)!');
    }
}
