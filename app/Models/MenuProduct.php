<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MenuProduct extends Pivot
{
    protected $table = 'menu_product';

    protected $fillable = [
        'menu_id',
        'product_id',
        'quantity',
    ];
}
