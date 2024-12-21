<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'discount_price',
        'promotion_start',
        'promotion_end',
        'description',
        'allergens',
        'image',
        'is_available',
        'category_id'
    ];

    protected $casts = [
        'allergens' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            // Generate slug if empty
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Polymorphic relation: one product can be an "orderable" item
    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    // Many-to-many with Menu
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_product')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    // Check if product is in discount period
    public function isOnPromotion()
    {
        return $this->discount_price !== null
            && $this->promotion_start <= now()
            && $this->promotion_end >= now();
    }

    // Avoid recursion by using $originalPrice
    public function getPriceAttribute($originalPrice)
    {
        return $this->isOnPromotion() ? $this->discount_price : $originalPrice;
    }
}
