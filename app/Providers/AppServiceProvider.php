<?php

namespace App\Providers;

use App\Models\Menu;
use App\Models\Product;
use App\Models\Address;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
       
        Schema::defaultStringLength(191);
        
        Relation::enforceMorphMap([
            'Menu' => Menu::class,
            'Product' => Product::class,
            'Address'=>Address::class,
        ]);
        
    }
}
