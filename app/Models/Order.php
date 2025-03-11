<?php

namespace App\Models;
use Illuminate\Support\Str;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'total_price',
        'status',
        'payment_method',
        'payment_status',
        'phone_number',
        'address_text',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = static::generateOrderNumber();
        });
    }

    public static function generateOrderNumber()
    {
        $prefix = 'MS-' . date('Ymd') . '-';
        $latest = static::where('order_number', 'like', $prefix . '%')
            ->latest('id')
            ->first();

        $sequence = $latest ? (int) str_replace($prefix, '', $latest->order_number) + 1 : 1;
        
        return $prefix . strtoupper(Str::random(4)) . $sequence;
    }
    

    public function customer()
    {
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
            'returned'=>'Retournée',
            default => 'Inconnu',
        };
    }

    // app/Models/Order.php
        public function getStatusColorAttribute(): string
        {
            return match($this->status) {
                'pending' => 'badge-neutral',
                'confirmed' => 'badge-info',
                'preparing' => 'badge-warning',
                'ready' => 'badge-success',
                'out_for_delivery' => 'badge-primary',
                'delivered' => 'badge-success',
                'cancelled' => 'badge-error',
                'returned' => 'badge-error',
                default => 'badge-neutral'
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
