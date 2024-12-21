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
            ['name' => 'California Rolls', 'description' => 'Rouleaux de sushi modernes.'],
            ['name' => 'Maki',            'description' => 'Rouleaux de sushi traditionnels.'],
            ['name' => 'Nigiri',          'description' => 'Riz avec tranche de poisson.'],
            ['name' => 'Condiments',      'description' => 'Accompagnements pour sushis.'],
            ['name' => 'Desserts',        'description' => 'Douceurs pour finir le repas.'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Categories seeded successfully!');
    }
}
