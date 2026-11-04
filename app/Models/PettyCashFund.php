<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class PettyCashFund extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'responsible_user_id',
        'department_id',
        'name',
        'code',
        'currency',
        'authorized_amount',
        'current_balance',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'authorized_amount' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function movements()
    {
        return $this->hasMany(PettyCashMovement::class)->latest('movement_date');
    }

    public function replenishments()
    {
        return $this->hasMany(PettyCashReplenishment::class);
    }

    public function getAvailableBalanceAttribute(): float
    {
        return (float) $this->current_balance;
    }

    public function getRemainingLimitAttribute(): float
    {
        return max(
            0,
            (float) $this->authorized_amount - (float) $this->current_balance
        );
    }
}
