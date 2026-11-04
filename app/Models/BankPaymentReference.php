<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankPaymentReference extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'bank_integration_id',
        'invoice_id',
        'reference',
        'external_reference',
        'amount',
        'currency',
        'status',
        'expires_at',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(BankIntegration::class, 'bank_integration_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
