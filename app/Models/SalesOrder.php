<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    protected $table = 'sales_orders';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = [
        'so_number',
        'customer',
        'order_date',
        'status',
    ];

    protected $casts = [
        'so_number' => 'string',
        'customer' => 'string',
        'order_date' => 'date',
        'status' => 'string',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SoItem::class, 'so_id', 'id');
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'so_id', 'id');
    }
}
