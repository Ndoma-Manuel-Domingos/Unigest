<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpensePayment extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'expense_id',
        'cash_register_id',
        'user_id',
        'cash_movement_id',
        'payment_number',
        'payment_date',
        'amount',
        'currency',
        'payment_method',
        'transaction_reference',
        'status',
        'observations',
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashMovement()
    {
        return $this->belongsTo(CashMovement::class);
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }
}
