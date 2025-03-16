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
        'notes',
        'delivery_fee',
        'return_reason',
        'returned_at'
    ];

    protected $casts = [
        'assigned_at'   => 'datetime',
        'picked_up_at'  => 'datetime',
        'delivered_at'  => 'datetime',
        'returned_at'   => 'datetime',
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

    public function isReturnable(): bool
    {
        return in_array($this->status, ['picked_up']);
    }

    /**
     * Get the translated delivery status.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'assigned' => 'Attribuée',
            'picked_up' => 'Récupérée',
            'delivered' => 'Livrée',
            'returned' => 'Retournée',
            default => 'Inconnu',
        };
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'assigned' => 'badge-secondary', 
            'picked_up' => 'badge-primary', 
            'delivered' => 'badge-success', 
            'returned' => 'badge-muted', 
            default => 'badge-neutral', 
        };
    }
}
