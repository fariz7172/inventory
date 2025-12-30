<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'rak_id',
        'variant_id',
        'quantity',
    ];

    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    /**
     * Get the reference code of the latest incoming movement for this stock.
     */
    public function getLatestReferenceAttribute()
    {
        $movement = \App\Models\StockMovement::where(function($q) {
                // Direct inbound
                $q->where('rak_id', $this->rak_id)
                  ->where('type', 'inbound');
            })
            ->orWhere(function($q) {
                // Transfer in or Outbound to this outlet
                $q->where('to_rak_id', $this->rak_id)
                  ->whereIn('type', ['transfer', 'outbound']);
            })
            ->where('variant_id', $this->variant_id)
            ->latest()
            ->first();

        return $movement ? $movement->reference_code : '-';
    }
}
