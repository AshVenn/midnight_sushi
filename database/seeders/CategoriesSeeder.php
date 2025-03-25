<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Californias'],
            ['name' => 'Makis'],
            ['name' => 'Nigiris'],
            ['name' => 'Desserts'],
            ['name' => 'Menus'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}