<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        // …autres champs éventuels
    ];

    /* ------------------------------------------------------------------ */
    /*  Relations                                                         */
    /* ------------------------------------------------------------------ */

    public function categories()
    {
        return $this->belongsToMany(Category::class)
                    ->withPivot('min_items', 'max_items')
                    ->withTimestamps();
    }

    /** Permet d’utiliser withAvg / withCount sur “reviews” */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
