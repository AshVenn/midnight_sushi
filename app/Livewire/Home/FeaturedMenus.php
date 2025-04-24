<?php

namespace App\Livewire\Home;

use Livewire\Component;
use App\Models\Menu;
use App\Models\MenuTemplate;

class FeaturedMenus extends Component
{
    public function render()
    {
        return view('livewire.home.featured-menus', [
            'featuredMenus'        => Menu::withAvg('reviews as reviews_avg_rating', 'rating')
                                          ->withCount('reviews')
                                          ->where('is_featured', true)
                                          ->take(3)
                                          ->get(),

            'featuredCustomMenus'  => MenuTemplate::withAvg('reviews as reviews_avg_rating', 'rating')
                                                  ->withCount('reviews')
                                                  ->where('is_featured', true)
                                                  ->take(3)
                                                  ->get(),
        ]);
    }
}
