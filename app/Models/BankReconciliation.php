<?php

namespace App\Models;


use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankReconciliation extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'bank_account_id',
        'created_by',
        'reference',
        'period_start',
        'period_end',
        'statement_opening_balance',
        'statement_closing_balance',
        'system_opening_balance',
        'system_closing_balance',
        'difference',
        'status',
        'reconciled_at',
        'reconciled_by',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',

        'statement_opening_balance' => 'decimal:2',
        'statement_closing_balance' => 'decimal:2',
        'system_opening_balance' => 'decimal:2',
        'system_closing_balance' => 'decimal:2',
        'difference' => 'decimal:2',

        'reconciled_at' => 'datetime',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reconciledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(BankStatementLine::class);
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isReconciled(): bool
    {
        return $this->status === 'reconciled';
    }

    public function pendingLines(): HasMany
    {
        return $this->lines()->where('status', 'pending');
    }

    public function matchedLines(): HasMany
    {
        return $this->lines()->where('status', 'matched');
    }
}
