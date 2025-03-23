<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class Review extends Model
{
    protected $fillable = [
        'user_id', 
        'reviewable_type',  
        'reviewable_id',   
        'rating',
    ];

    public function reviewable()
    {
        return $this->morphTo();
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Détermine en SQL brute si tous les articles d'une commande
     * ont déjà été notés par l'utilisateur.
     *
     * @param  \App\Models\Order  $order
     * @param  int  $userId
     * @return bool
     */
    public static function isOrderReviewed($order, int $userId): bool
{
    return !DB::table('order_items')
        ->where('order_id', $order->id)
        ->whereNotExists(function ($query) use ($userId) {
            $query->select(DB::raw(1))
                  ->from('reviews')
                  ->whereColumn('reviews.reviewable_id', 'order_items.orderable_id')
                  ->whereColumn('reviews.reviewable_type', 'order_items.orderable_type')
                  ->where('reviews.user_id', $userId);
        })
        ->exists();
}
}
