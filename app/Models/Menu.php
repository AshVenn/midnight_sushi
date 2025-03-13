<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'discount_price',
        'promotion_start',
        'promotion_end',
        'image',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($menu) {
            if (empty($menu->slug)) {
                $menu->slug = Str::slug($menu->name);
            }
        });
    }

    // Polymorphic relation: a menu can be part of order_items
    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    // Many-to-many with Products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'menu_product')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function isOnPromotion()
    {
        return $this->discount_price !== null
            && $this->promotion_start <= now()
            && $this->promotion_end >= now();
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
    
}

