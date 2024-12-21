<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersSeeder::class,            // Seed users first (required for relationships with orders and deliveries)
            AddressesSeeder::class,       // Seed addresses (required for deliveries)
            CategoriesSeeder::class,      // Seed categories (required for products)
            ProductsSeeder::class,        // Seed products (depends on categories)
            MenuSeeder::class,            // Seed menus (depends on products),
            OrdersSeeder::class,          // Seed orders (depends on users)
            OrderItemsSeeder::class,      // Seed order items (depends on orders and products)
            DeliveriesSeeder::class,      // Seed deliveries (depends on orders and addresses)
        ]);
    }
}
