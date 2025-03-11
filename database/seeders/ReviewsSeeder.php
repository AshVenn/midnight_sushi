<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Menu;

class ReviewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $products = Product::all();
        $menus = Menu::all();

       
        if ($users->isEmpty() || ($products->isEmpty() && $menus->isEmpty())) {
            $this->command->warn("Skipping review seeding: Not enough users, products, or menus.");
            return;
        }

        for ($i = 0; $i < 30; $i++) {
            $user = $users->random();

            
            $reviewable = rand(0, 1) ? $products->random() : $menus->random();

           
            if (Review::where('user_id', $user->id)
                ->where('reviewable_id', $reviewable->id)
                ->where('reviewable_type', get_class($reviewable))
                ->exists()) {
                continue; 
            }

            
            Review::create([
                'user_id' => $user->id,
                'reviewable_id' => $reviewable->id,
               'reviewable_type' => $reviewable instanceof Product ? 'App\\Models\\Product' : 'App\\Models\\Menu',
                'rating' => rand(1, 5),
                'comment' => fake()->sentence(),
            ]);
        }
    }
}
