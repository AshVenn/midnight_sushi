<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'recipient_name',
        'street_address',
        'city',
        'postal_code',
        'country',
    ];

    /**
     * Define relationship to the Order model.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    /**
     * Get the full address as a formatted string.
     *
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        return "{$this->recipient_name}, {$this->street_address}, {$this->city}, {$this->postal_code}, {$this->country}";
    }

}
