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
        // Get only customers
        $customers = User::where('role', 'customer')->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Skipping OrdersSeeder.');
            return;
        }

        // Make sure we have products or menus to attach
        $productCount = Product::count();
        $menuCount    = Menu::count();
        if ($productCount === 0 && $menuCount === 0) {
            $this->command->warn('No products or menus found. Skipping OrdersSeeder.');
            return;
        }

        // Create e.g. 30 orders total
        Order::factory(30)->create()->each(function ($order) use ($customers) {
            // Ensure this order belongs to a random customer, never admin/manager/delivery
            $order->customer_id = $customers->random()->id;
            $order->save();
        });

        $this->command->info('Orders seeded successfully!');
    }
}
