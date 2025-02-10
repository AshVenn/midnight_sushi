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

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'preparing' => 'En préparation',
            'ready' => 'Prête',
            'out_for_delivery' => 'En livraison',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => 'Inconnu',
        };
    }
    
    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }
    
    public function getHasStartedPreparationAttribute(): bool
    {
        return !in_array($this->status, ['pending', 'confirmed']);
    }
}
