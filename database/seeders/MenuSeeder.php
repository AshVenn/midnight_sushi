<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Product;

class MenuSeeder extends Seeder
{
    public function run()
    {
        $menus = [
            [
                'name' => 'Menu California S',
                'price' => 20.00,
                'description' => '8 californias au choix, une boisson et un dessert',
                'image' => 'images/midnight_sushi/menu-california-s.jpg'
            ],
            [
                'name' => 'Menu Mixte M',
                'price' => 39.50,
                'description' => '3 nigiris, 6 makis, 6 californias, une boisson et un dessert',
                'image' => 'images/midnight_sushi/menu-mixte-m.jpg'
            ],
        ];

        foreach ($menus as $menuData) {
            $menu = Menu::create([
                'name' => $menuData['name'],
                'price' => $menuData['price'],
                'description' => $menuData['description'],
                'image' => $menuData['image']
            ]);
            
            $products = Product::inRandomOrder()
                ->take($menuData['name'] === 'Menu California S' ? 8 : 15) // Adjust based on menu needs
                ->get();

            foreach ($products as $product) {
                $menu->products()->attach($product->id, [
                    'quantity' => rand(1, 3)
                ]);
            }
        }
    }
}