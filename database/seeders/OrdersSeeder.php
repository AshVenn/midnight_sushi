<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Menu;

class OrdersSeeder extends Seeder
{
    public function run()
    {
        // Check if we have any customers
        $customerCount = User::where('role', 'customer')->count();
        if ($customerCount === 0) {
            $this->command->warn('No customers found. Skipping OrdersSeeder.');
            return;
        }

        // Check if we have any products or menus
        $productCount = Product::count();
        $menuCount = Menu::count();

        if ($productCount === 0 && $menuCount === 0) {
            $this->command->warn('No products or menus found. Skipping OrdersSeeder.');
            return;
        }

        // Create 30 random orders, each assigned to a random customer
        // (assuming you have an OrderFactory that automatically picks a user,
        //  or you can do a manual approach)
        Order::factory(30)->create()->each(function ($order) use ($productCount, $menuCount) {
            // Decide how many products and menus to attach
            // For example, 1-5 random products, 0-2 random menus
            $products = Product::inRandomOrder()->take(rand(1, 5))->get();
            $menus    = Menu::inRandomOrder()->take(rand(0, 2))->get();

            // Merge them into a single collection
            $items = $products->concat($menus);

            foreach ($items as $item) {
                // For each item, store the polymorphic reference
                $order->orderItems()->create([
                    'orderable_type' => get_class($item),   // App\Models\Product or App\Models\Menu
                    'orderable_id'   => $item->id,
                    'quantity'       => rand(1, 5),
                    'price'          => $item->price,       // Using the model's price attribute
                ]);
            }
        });

        $this->command->info('Orders seeded successfully with random Product & Menu items!');
    }
}
