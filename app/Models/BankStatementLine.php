<?php

namespace App\Models;


use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankStatementLine extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'bank_reconciliation_id',
        'matched_bank_movement_id',
        'transaction_date',
        'description',
        'reference',
        'type',
        'amount',
        'status',
        'matched_at',
        'matched_by',
        'raw_data',
        'observations',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'matched_at' => 'datetime',
        'raw_data' => 'array',
    ];

    public function reconciliation(): BelongsTo
    {
        return $this->belongsTo(
            BankReconciliation::class,
            'bank_reconciliation_id'
        );
    }

    public function matchedBankMovement(): BelongsTo
    {
        return $this->belongsTo(
            BankMovement::class,
            'matched_bank_movement_id'
        );
    }

    public function matchedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'matched_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isMatched(): bool
    {
        return $this->status === 'matched';
    }

    public function isIgnored(): bool
    {
        return $this->status === 'ignored';
    }

    public function isIncome(): bool
    {
        return $this->type === 'income';
    }

    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }
}
