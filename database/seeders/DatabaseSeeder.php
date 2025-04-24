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
            SettingsSeeder::class,
            UsersSeeder::class,
            CategoriesSeeder::class,
            ProductsSeeder::class,
            MenuSeeder::class,
            AddressesSeeder::class,
            OrdersSeeder::class,
            OrderItemsSeeder::class,
            DeliveriesSeeder::class,
            ReviewsSeeder::class,
            ContactInquiriesSeeder::class,
            MenuTemplatesSeeder::class,
        ]);
    }
}
