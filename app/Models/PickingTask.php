<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PickingTask extends Model
{
    protected $table = 'picking_tasks';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = [
        'so_item_id',
        'bind_id',
        'quantity_to_pick',
        'operator_id',
        'status',
        'picked_at',
    ];

    protected $casts = [
        'so_item_id' => 'integer',
        'bind_id' => 'integer',
        'quantity_to_pick' => 'integer',
        'operator_id' => 'integer',
        'status' => 'string',
        'picked_at' => 'date',
    ];

    public function soitems(): BelongsTo
    {
        return $this->BelongsTo(SoItem::class, 'so_item_id', 'id');
    }

    public function users(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'operator_id', 'id');
    }

    public function storagebins(): BelongsTo
    {
        return $this->belongsTo(StorageBin::class, 'bind_id', 'id');
    }
}
