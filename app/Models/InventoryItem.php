<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_id',
        'rak_id',
        'serial_number',
        'status',
        'inbound_id',
        'outbound_id',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }

    public function inboundMovement()
    {
        return $this->belongsTo(StockMovement::class, 'inbound_id');
    }

    public function outboundMovement()
    {
        return $this->belongsTo(StockMovement::class, 'outbound_id');
    }
}
