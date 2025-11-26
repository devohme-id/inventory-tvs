<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InboundItem extends Model
{
    protected $table = 'inbound_items';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];
    // public $timestamps = false;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'quantity_expected',
        'quantity_received',
        'bind_id',
        'status',
    ];

    protected $casts = [
        'invoice_id' => 'integer',
        'product_id' => 'integer',
        'quantity_expected' => 'integer',
        'quantity_received' => 'integer',
        'bind_id' => 'integer',
        'status' => 'string',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(InboundInvoice::class, 'invoice_id', 'id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function bin(): BelongsTo
    {
        return $this->belongsTo(StorageBin::class, 'bind_id', 'id');
    }
}
