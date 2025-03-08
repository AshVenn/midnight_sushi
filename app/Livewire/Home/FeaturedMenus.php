<?php

namespace App\Livewire\Home;

use App\Models\Menu;
use Livewire\Component;

class FeaturedMenus extends Component
{
    public $featuredMenus = [];

    public function mount()
    {
        // Fetch top 3 menus based on reviews
        $this->featuredMenus = Menu::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(3)
            ->get();

        // If less than 3 menus are found, add random ones
        if ($this->featuredMenus->count() < 3) {
            $extraMenus = Menu::inRandomOrder()->take(3 - $this->featuredMenus->count())->get();
            $this->featuredMenus = $this->featuredMenus->concat($extraMenus);
        }
    }

    public function render()
    {
        return view('livewire.home.featured-menus', [
            'featuredMenus' => $this->featuredMenus,
        ]);
    }
}
