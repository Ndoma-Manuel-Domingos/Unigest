<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class TreasuryDailyClosure extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'closure_date',
        'opening_balance',
        'total_income',
        'total_expense',
        'expected_balance',
        'counted_balance',
        'difference',
        'status',
        'closed_by',
        'closed_at',
        'notes',
    ];

    protected $casts = [
        'closure_date' => 'date',
        'opening_balance' => 'decimal:2',
        'total_income' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'counted_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'closed_at' => 'datetime',
    ];

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }
}
