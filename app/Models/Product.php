<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

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
        'promotion_start' => 'datetime',
        'promotion_end' => 'datetime',
        'is_available' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    /**
     * Relationship with Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Polymorphic relation: one product can be an "orderable" item
     */
    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }

    /**
     * Many-to-many relationship with Menu
     */
    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_product')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    /**
     * Check if product is currently on promotion
     */
    public function isOnPromotion(): bool
    {
        return !is_null($this->discount_price) &&
            (!$this->promotion_start || $this->promotion_start->isPast()) &&
            (!$this->promotion_end || $this->promotion_end->isFuture());
    }

    /**
     * Accessor for Price with Promotion Check
     */
    public function getFinalPriceAttribute(): float
    {
        return $this->isOnPromotion() ? $this->discount_price : $this->price;
    }
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public static function topSellingProducts($limit = 5)
    {
        return DB::table('products as p')
            ->select('p.name', 'p.price', DB::raw("COUNT(oi.id) as total_sales"))
            ->leftJoin('order_items as oi', function($join) {
                $join->on('oi.orderable_id', '=', 'p.id')
                     ->where('oi.orderable_type', '=', 'App\Models\Product');
            })
            ->leftJoin('orders as o', function($join) {
                $join->on('o.id', '=', 'oi.order_id')
                     ->where('o.status', '<>', 'cancelled');
            })
            ->groupBy('p.id', 'p.name', 'p.price')
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->get();
    }
}
