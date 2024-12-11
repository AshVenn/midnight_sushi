<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 
        'delivery_person_id', 
        'shipping_address_id',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
    ];

    // Relationship to the customer (user with role "customer")
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Relationship to the delivery person (user with role "delivery_person")
    public function deliveryPerson()
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
