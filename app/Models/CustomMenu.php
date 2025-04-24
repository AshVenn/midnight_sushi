<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomMenu extends Model
{
    protected $fillable = [
        'template_id',
        'user_id',
        'is_featured',     // ← nouveau
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    /* Relations ---------------------------------------------------------- */

    public function template()
    {
        return $this->belongsTo(MenuTemplate::class, 'template_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'custom_menu_product')
                    ->withPivot('category_id', 'quantity')
                    ->withTimestamps();
    }
    /* Prix = prix du template  ------------------------------------------ */

    public function getPriceAttribute(): float
    {
        return $this->template->price;
    }
}
