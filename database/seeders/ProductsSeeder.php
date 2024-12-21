<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 20 random products
        Product::factory()
            ->count(20)
            ->create();

        $this->command->info('Products seeded successfully (20 products)!');
    }
}
