<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PettyCashMovement extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'petty_cash_fund_id',
        'user_id',
        'approved_by',
        'movement_number',
        'type',
        'status',
        'amount',
        'description',
        'document_number',
        'supplier_name',
        'movement_date',
        'receipt_path',
        'approved_at',
        'notes',
        'approval_notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'movement_date' => 'date',
            'approved_at' => 'datetime',
        ];
    }

    public function fund()
    {
        return $this->belongsTo(PettyCashFund::class, 'petty_cash_fund_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function replenishment()
    {
        return $this->hasOne(PettyCashReplenishment::class, 'movement_id');
    }
}
