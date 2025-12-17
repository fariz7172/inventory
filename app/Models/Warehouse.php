<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    const STATUS_WAREHOUSE = 1;
    const STATUS_OUTLET = 2;

    protected $fillable = [
        'name',
        'location',
        'description',
        'status',
    ];

    public function raks()
    {
        return $this->hasMany(Rak::class);
    }

    /**
     * Scope for warehouses only
     */
    public function scopeWarehouses($query)
    {
        return $query->where('status', self::STATUS_WAREHOUSE);
    }

    /**
     * Scope for outlets only
     */
    public function scopeOutlets($query)
    {
        return $query->where('status', self::STATUS_OUTLET);
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return $this->status == self::STATUS_WAREHOUSE ? 'Warehouse' : 'Outlet';
    }
}
