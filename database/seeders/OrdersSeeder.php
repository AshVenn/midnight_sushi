<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use App\Models\Menu;

class OrdersSeeder extends Seeder
{
    public function run(): void
    {
        // clients uniquement
        $customers = User::where('role', 'customer')->get();
        if ($customers->isEmpty()) {
            $this->command->warn('No customers found. Skipping OrdersSeeder.');
            return;
        }

        // s’assurer qu’il y a au moins un produit ou un menu
        $productCount = Product::count();
        $menuCount    = Menu::count();
        if ($productCount === 0 && $menuCount === 0) {
            $this->command->warn('No products or menus found. Skipping OrdersSeeder.');
            return;
        }

        // 30 commandes
        Order::factory(30)->create()->each(function ($order) use ($customers) {
            $order->customer_id = $customers->random()->id; // jamais admin/manager etc.
            $order->save();
        });

        $this->command->info('Orders seeded successfully!');
    }
}
