<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankApiTransaction extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'bank_integration_id',
        'bank_account_id',
        'bank_movement_id',
        'external_transaction_id',
        'reference',
        'description',
        'type',
        'amount',
        'currency',
        'occurred_at',
        'status',
        'raw_payload',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'occurred_at' => 'datetime',
        'raw_payload' => 'array',
    ];

    protected $hidden = [
        'raw_payload',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(BankIntegration::class, 'bank_integration_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function bankMovement(): BelongsTo
    {
        return $this->belongsTo(BankMovement::class);
    }
}
