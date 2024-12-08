<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Nigiri', 'description' => 'Hand-formed sushi with a slice of fish over rice.'],
            ['name' => 'Sashimi', 'description' => 'Slices of raw fish served without rice.'],
            ['name' => 'Maki', 'description' => 'Rolled sushi with seaweed on the outside.'],
            ['name' => 'Uramaki', 'description' => 'Inside-out sushi with rice on the outside.'],
            ['name' => 'Temaki', 'description' => 'Hand-rolled sushi in a cone shape.'],
            ['name' => 'Chirashi', 'description' => 'Bowl of sushi rice topped with sashimi.'],
            ['name' => 'Oshizushi', 'description' => 'Pressed sushi formed in a box mold.'],
            ['name' => 'Futomaki', 'description' => 'Thick sushi rolls filled with multiple ingredients.'],
            ['name' => 'Inarizushi', 'description' => 'Fried tofu pouches filled with sushi rice.'],
            ['name' => 'Katsu Sushi', 'description' => 'Sushi with crispy fried cutlets.'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
