<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\Menu;
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
        $menus = Menu::all();

        if ($orders->isEmpty() || ($products->isEmpty() && $menus->isEmpty())) {
            $this->command->warn('No Orders, Products, or Menus found. Skipping OrderItemsSeeder.');
            return;
        }

        foreach ($orders as $order) {
            $itemsCount = rand(1, 5);
            for ($i = 0; $i < $itemsCount; $i++) {
                // Randomly decide whether to add a product or a menu
                $isProduct = rand(0, 1) === 1;
                
                if ($isProduct && !$products->isEmpty()) {
                    $orderable = $products->random();
                    $orderableType = 'Product'; // ✅ Matches MorphMap
                } elseif (!$menus->isEmpty()) {
                    $orderable = $menus->random();
                    $orderableType = 'Menu'; // ✅ Matches MorphMap
                } else {
                    continue; // Skip if no valid items exist
                }

                // Create a new OrderItem using the polymorphic columns
                OrderItem::create([
                    'order_id'       => $order->id,
                    'orderable_type' => $orderableType, // ✅ Now matches morphMap in AppServiceProvider
                    'orderable_id'   => $orderable->id,
                    'quantity'       => rand(1, 3),
                    'price'          => $orderable->price,
                ]);
            }
        }

        $this->command->info('OrderItems seeded successfully (polymorphic items added)!');
    }
}
