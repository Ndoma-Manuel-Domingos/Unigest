<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class CashCount extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'cash_register_id',
        'counted_by',
        'reviewed_by',
        'reference',
        'denominations',
        'expected_balance',
        'counted_balance',
        'difference',
        'status',
        'counted_at',
        'reviewed_at',
        'notes',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'denominations' => 'array',
            'expected_balance' => 'decimal:2',
            'counted_balance' => 'decimal:2',
            'difference' => 'decimal:2',
            'counted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function counter()
    {
        return $this->belongsTo(User::class, 'counted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
