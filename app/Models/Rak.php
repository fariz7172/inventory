<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rak extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'name',
        'location_code', 
        'location', // Restored
        'capacity',
        'description',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function inventoryStocks()
    {
        return $this->hasMany(InventoryStock::class);
    }
}
