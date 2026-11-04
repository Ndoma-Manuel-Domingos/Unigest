<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialAuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'module',
        'event',
        'auditable_type',
        'auditable_id',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
