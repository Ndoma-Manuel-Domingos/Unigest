<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankWebhookLog extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'bank_integration_id',
        'external_event_id',
        'event_type',
        'payload',
        'signature_valid',
        'status',
        'attempts',
        'received_at',
        'processed_at',
        'error_message',
    ];

    protected $casts = [
        'payload' => 'array',
        'signature_valid' => 'boolean',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected $hidden = [
        'payload',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(BankIntegration::class, 'bank_integration_id');
    }
}
