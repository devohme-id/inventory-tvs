<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InboundInvoice extends Model
{
    protected $table = 'inbound_invoices';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];
    // public $timestamps = false;

    protected $fillable = [
        'invoice_number',
        'supplier',
        'received_at',
        'status',
        'user_id',
    ];

    protected $casts = [
        'invoice_number' => 'string',
        'supplier' => 'string',
        'received_at' => 'date',
        'status' => 'string',
        'user_id' => 'integer',
    ];

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InboundItem::class, 'invoice_id', 'id');
    }
}
