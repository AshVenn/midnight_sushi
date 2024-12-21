<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'delivery_person_id',
        'status',
        'assigned_at',
        'picked_up_at',
        'delivered_at',
        'address_text',
        'phone_number',
        'notes',        // new notes field for delivery
        'delivery_fee',
    ];

    protected $casts = [
        'assigned_at'   => 'datetime',
        'picked_up_at'  => 'datetime',
        'delivered_at'  => 'datetime',
        'delivery_fee'  => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(User::class, 'delivery_person_id');
    }
}
