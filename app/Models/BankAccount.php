<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'bank_name',
        'account_number',
        'iban',
        'swift',
        'currency',
        'opening_balance',
        'current_balance',
        'is_active',
        'is_default',
        'branch',
        'notes',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function movements(): HasMany
    {
        return $this->hasMany(BankMovement::class);
    }
    
    public function getBalanceAttribute(): float
    {
        return (float) $this->current_balance;
    }

    public function reconciliations(): HasMany
    {
        return $this->hasMany(BankReconciliation::class);
    }

    public function integrations()
    {
        return $this->hasMany(BankIntegration::class);
    }

}
