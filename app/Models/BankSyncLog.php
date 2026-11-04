<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankSyncLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'bank_integration_id',
        'status',
        'started_at',
        'finished_at',
        'records_found',
        'records_imported',
        'records_ignored',
        'cursor',
        'error_message',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(BankIntegration::class, 'bank_integration_id');
    }
}
