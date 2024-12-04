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
        // Seed additional tables
        $this->call([
            CategoriesSeeder::class,
            ProductsSeeder::class,
            OrdersSeeder::class,
            ReviewsSeeder::class,
            ContactInquiriesSeeder::class,
            DeliveriesSeeder::class,
        ]);

        // Optional: Add a test user
        \App\Models\User::factory()->create([
            'first_name' => 'john',
            'last_name' => 'doe',
            'email' => 'test@example.com',
            'role' => 'admin', 
        ]);
    }
}
