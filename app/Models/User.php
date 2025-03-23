<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class User extends Authenticatable implements MustVerifyEmail

{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role',
        'phone_number',
        'avatar',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class, 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
    
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function deliveries()
        {
            return $this->hasManyThrough(Delivery::class, Order::class, 'customer_id', 'order_id');
        }

    public function pendingReviews()
        {
            return $this->hasManyThrough(OrderItem::class, Order::class, 'customer_id')
                ->whereDoesntHave('review');
        }
    public function reviews()
        {
            return $this->hasMany(Review::class);
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Actif',
            'inactive' => 'Inactif',
            'banned' => 'Banni',
            default => 'Inconnu',
        };
    }
    
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'active' => 'text-green-600 border-green-600',
            'inactive' => 'text-gray-600 border-gray-600',
            'banned' => 'text-red-600 border-red-600',
            default => 'text-neutral-600 border-neutral-600',
        };
    }

    public function getRoleTextAttribute(): string
        {
            return match ($this->role) {
                'admin' => 'Administrateur',
                'manager' => 'Responsable',
                'delivery_person' => 'Livreur',
                'customer' => 'Client',
                default => 'Inconnu',
            };
        }

        public function getRoleColorAttribute(): string
        {
            return match ($this->role) {
                'admin' => 'text-purple-600 border-purple-600',
                'manager' => 'text-blue-600 border-blue-600',
                'delivery_person' => 'text-orange-600 border-orange-600',
                'customer' => 'text-gray-600 border-gray-600',
                default => 'text-neutral-600 border-neutral-600',
            };
        }

    
}
