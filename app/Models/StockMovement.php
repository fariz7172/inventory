<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'variant_id',
        'rak_id',
        'to_rak_id',
        'user_id',
        'quantity',
        'reference_code',
        'description',
    ];

    public function variant()
    {
        return $this->belongsTo(Variant::class);
    }

    public function rak()
    {
        return $this->belongsTo(Rak::class);
    }

    public function toRak()
    {
        return $this->belongsTo(Rak::class, 'to_rak_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get items associated with this movement (for inbound)
     */
    public function inboundItems()
    {
        return $this->hasMany(InventoryItem::class, 'inbound_id');
    }

    /**
     * Get items associated with this movement (for outbound)
     */
    public function outboundItems()
    {
        return $this->hasMany(InventoryItem::class, 'outbound_id');
    }

    /**
     * Get all items for this movement based on type
     */
    public function items()
    {
        if ($this->type === 'inbound') {
            return $this->inboundItems();
        }
        return $this->outboundItems();
    }
}
