<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class CashTransfer extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'source_cash_register_id',
        'destination_cash_register_id',
        'initiated_by',
        'received_by',
        'transfer_number',
        'amount',
        'status',
        'transfer_date',
        'received_at',
        'notes',
        'receiver_notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transfer_date' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function sourceCashRegister()
    {
        return $this->belongsTo(CashRegister::class, 'source_cash_register_id');
    }

    public function destinationCashRegister()
    {
        return $this->belongsTo(CashRegister::class, 'destination_cash_register_id');
    }

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
