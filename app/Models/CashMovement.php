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

    protected static function booted(): void
    {
        static::updating(function (CashMovement $movement) {
            $protectedFields = [
                'cash_register_id',
                'payment_id',
                'reference_payment_id',
                'type',
                'source',
                'description',
                'amount',
                'movement_date',
            ];

            $changed = collect($protectedFields)
                ->contains(fn ($field) => $movement->isDirty($field));

            $isProtected = in_array(
                $movement->source,
                ['payment', 'payment_reversal'],
                true
            );

            if ($changed && $isProtected) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'movement' => 'Este movimento de caixa foi gerado por um pagamento e não pode ser alterado manualmente.',
                ]);
            }
        });

        static::deleting(function (CashMovement $movement) {
            if (in_array($movement->source, ['payment', 'payment_reversal'], true)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'movement' => 'Este movimento de caixa não pode ser eliminado manualmente.',
                ]);
            }
        });
    }


}
