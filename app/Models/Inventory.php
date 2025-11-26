<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];
    // public $timestamps = false;

    protected $fillable = [
        'product_id',
        'bind_id',
        'quantity',
        'last_updated',
    ];

    protected $casts = [
        'product_id' => 'integer',
        'bind_id' => 'integer',
        'quantity' => 'integer',
        'last_updated' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function bin(): BelongsTo
    {
        return $this->belongsTo(StorageBin::class, 'bind_id', 'id');
    }
}
