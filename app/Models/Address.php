<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recipient_name',
        'street_address',
        'city',
        'postal_code',
        'country',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute(): string
    {
        return "{$this->recipient_name}, {$this->street_address}, {$this->city}, {$this->postal_code}, {$this->country}";
    }
}
