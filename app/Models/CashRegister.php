<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'user_id',
        'opened_at',
        'closed_at',
        'opening_balance',
        'expected_balance',
        'counted_balance',
        'difference',
        'status',
        'opening_notes',
        'closing_notes',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'expected_balance' => 'decimal:2',
        'counted_balance' => 'decimal:2',
        'difference' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function getTotalIncomeAttribute(): float
    {
        return (float) $this->movements()
            ->where('type', 'income')
            ->sum('amount');
    }

    public function getTotalExpenseAttribute(): float
    {
        return (float) $this->movements()
            ->where('type', 'expense')
            ->sum('amount');
    }
}
