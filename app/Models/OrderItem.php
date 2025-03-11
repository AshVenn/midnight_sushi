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

    public function orderable()
    {
        return $this->morphTo();
    }


    public function getOrderableTypeAttribute($value)
    {
        return class_basename($value);
    }

    public function hasReview(): bool
    {
        return Review::where([
            'user_id'        => $this->order->customer_id,
            'reviewable_id'  => $this->orderable_id,
            'reviewable_type'=> $this->orderable_type,
        ])->exists();
    }
    
}