<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PettyCashReplenishment extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'petty_cash_fund_id',
        'requested_by',
        'reviewed_by',
        'movement_id',
        'request_number',
        'requested_amount',
        'approved_amount',
        'status',
        'request_date',
        'reviewed_at',
        'paid_at',
        'reason',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'requested_amount' => 'decimal:2',
            'approved_amount' => 'decimal:2',
            'request_date' => 'date',
            'reviewed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function fund()
    {
        return $this->belongsTo(PettyCashFund::class, 'petty_cash_fund_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function movement()
    {
        return $this->belongsTo(PettyCashMovement::class, 'movement_id');
    }
}
