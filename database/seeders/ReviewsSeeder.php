<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class ReviewsSeeder extends Seeder
{
    public function run()
    {
        
        $customers = User::where('role', 'customer')->get();
        
        
        Product::all()->each(function ($product) use ($customers) {
            $this->createReviewsForItem($product, $customers);
        });

     
        Menu::all()->each(function ($menu) use ($customers) {
            $this->createReviewsForItem($menu, $customers);
        });
    }

    private function createReviewsForItem($item, $customers)
    {
        foreach ($customers as $customer) {
            Review::create([
                'user_id'         => $customer->id,
                'reviewable_id'   => $item->id,
                'reviewable_type' => $item instanceof Product ? 'Product' : 'Menu',
                'rating'          => 5,
            ]);
        }
    }
}
