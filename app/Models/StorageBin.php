<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageBin extends Model
{
    protected $table = 'storage_bins';

    protected $primaryKey = 'id';

    protected $guarded = ['id'];

    protected $fillable = [
        'bin_code',
        'rack',
        'level',
        'slot',
        'bin_type',
        'is_empty',
    ];

    protected $casts = [
        'bin_code' => 'string',
        'rack' => 'string',
        'level' => 'integer',
        'slot' => 'integer',
        'bin_type' => 'string',
        'is_empty' => 'boolean',
    ];

    // public $timestamps = false;

    public function scopeEmpty($query)
    {
        return $query->where('is_empty', true);
    }


    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class, 'bind_id', 'id');
    }
}
