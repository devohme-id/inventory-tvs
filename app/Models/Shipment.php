<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Shipment extends Model
{
    protected $table = 'shipments';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = [
        'so_id',
        'box_id',
        'total_weight_kg',
        'operator_id',
        'shipped_at',
    ];

    protected $casts = [
        'so_id' => 'integer',
        'box_id' => 'string',
        // 'total_weight_kg' => 'decimal',
        'operator_id' => 'integer',
        'shipped_at' => 'datetime',
    ];

    public function salesorders(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'so_id', 'id');
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id', 'id');
    }
}
