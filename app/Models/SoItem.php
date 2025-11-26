<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne; // Tambahkan ini

class SoItem extends Model
{
    protected $table = 'so_items';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = [
        'so_id',
        'product_id',
        'quantity_ordered',
        'quantity_picked',
        'quantity_packed',
    ];

    protected $casts = [
        'so_id' => 'integer',
        'product_id' => 'integer',
        'quantity_ordered' => 'integer',
        'quantity_picked' => 'integer',
        'quantity_packed' => 'integer',
    ];

    public function salesorders(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class, 'so_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    // --- TAMBAHAN BARU ---
    public function pickingTask(): HasOne
    {
        return $this->hasOne(PickingTask::class, 'so_item_id', 'id');
    }
}
