<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'orderable_type',
        'orderable_id',
        'quantity',
        'price',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Polymorphic relation: product or menu
    public function orderable()
    {
        return $this->morphTo();
    }

    /**
     * Optional convenience relationships
     * (let's you do $orderItem->product or $orderItem->menu)
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'orderable_id')
                    ->where('orderable_type', \App\Models\Product::class);
    }

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'orderable_id')
                    ->where('orderable_type', \App\Models\Menu::class);
    }
}
