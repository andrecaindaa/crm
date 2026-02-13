<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
    'name',
    'price',
    ];

    public function deals()
    {
        return $this->belongsToMany(Deal::class, 'deal_products')
            ->withPivot(['quantity', 'unit_price', 'total'])
            ->withTimestamps();
    }

}
