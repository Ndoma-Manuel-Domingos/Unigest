<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashMovement extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'cash_register_id',
        'user_id',
        'payment_id',
        'reference_payment_id',
        'type',
        'source',
        'description',
        'amount',
        'movement_date',
        'observations',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'movement_date' => 'datetime',
    ];

    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function referencePayment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'reference_payment_id');
    }
}
