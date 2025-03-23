<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;

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
    public static function topSellingMenus($limit = 5)
    {
        return DB::table('menus as m')
            ->select('m.name', 'm.price', DB::raw("COUNT(oi.id) as total_sales"))
            ->leftJoin('order_items as oi', function($join) {
                $join->on('oi.orderable_id', '=', 'm.id')
                     ->where('oi.orderable_type', '=', 'App\Models\Menu');
            })
            ->leftJoin('orders as o', function($join) {
                $join->on('o.id', '=', 'oi.order_id')
                     ->where('o.status', '<>', 'cancelled');
            })
            ->groupBy('m.id', 'm.name', 'm.price')
            ->orderByDesc('total_sales')
            ->limit($limit)
            ->get();
    }
}

