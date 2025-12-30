<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'model',
        'price_buy',
        'price_sell',
        'description',
        'image',
    ];

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }
}
