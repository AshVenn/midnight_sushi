<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
        'phone_number',
        'address_text',
        'notes', // new notes field
    ];

    public function customer()
    {
        // Assumes you have a User model (App\Models\User)
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship for deliveries
    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }

    public function currentDelivery()
    {
        // Example usage: find the latest assigned/picked_up
        return $this->deliveries()
                    ->whereIn('status', ['assigned', 'picked_up'])
                    ->orderByDesc('id')
                    ->first();
    }
}
