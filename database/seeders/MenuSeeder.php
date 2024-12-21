<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Product;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productCount = Product::count();

        if ($productCount === 0) {
            $this->command->warn('No products found, skipping Menu seeding.');
            return;
        }

        // Create some menus
        $menus = Menu::factory()->count(10)->create();

        foreach ($menus as $menu) {
            // Attach random products to each menu
            $randomProducts = Product::inRandomOrder()->take(rand(1, 5))->get();
            foreach ($randomProducts as $product) {
                $menu->products()->attach($product->id, [
                    'quantity' => rand(1, 3),
                ]);
            }
        }

        $this->command->info('Menus seeded successfully with pivot data (menu_product)!');
    }
}
