<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    // Kita hanya butuh created_at untuk log
    // public $timestamps = false;

    protected $fillable = [
        'user_id',
        'action',
        'details',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Relasi ke User (Pelaku)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Helper static untuk mencatat log dengan mudah
    // Cara pakai: AuditLog::record('Create Product', 'Menambahkan produk baru: Ban Luar', $userId);
    public static function record($action, $details = null, $userId = null)
    {
        self::create([
            'user_id' => $userId ?? auth()->id(),
            'action' => $action,
            'details' => $details,
        ]);
    }
}
