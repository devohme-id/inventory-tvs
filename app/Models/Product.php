<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $table = 'products';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = [
        'part_number',
        'description',
        'category',
        'weight_kg',
    ];

    protected $casts = [
        'part_number' => 'string',
        'description' => 'string',
        'category' => 'string',
        // 'weight_kg' => 'decimal',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'product_id', 'id');
    }

    public function inboundItems(): HasMany
    {
        return $this->hasMany(InboundItem::class, 'product_id', 'id');
    }
}
