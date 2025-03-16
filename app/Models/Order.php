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
                'returned' => 'Retournée',
                default => 'Inconnu',
            };
        }

        public function getStatusColorAttribute(): string
        {
            return match($this->status) {
                'pending' => 'badge-secondary', // Cool gray tone
                'confirmed' => 'badge-info', // Soft blue for confirmation
                'preparing' => 'badge-warning', // Warm yellow to indicate action
                'ready' => 'badge-primary', // Strong blue indicating readiness
                'out_for_delivery' => 'badge-accent', // Bright cyan to indicate movement
                'delivered' => 'badge-success', // Green for successful delivery
                'cancelled' => 'badge-error', // Red for cancellation
                'returned' => 'badge-muted', // Soft gray to indicate a returned order
                default => 'badge-neutral', // Neutral for unknown statuses
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
