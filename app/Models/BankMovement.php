<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankMovement extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'bank_account_id',
        'user_id',
        'type',
        'reference',
        'description',
        'amount',
        'movement_date',
        'document_number',
        'transaction_reference',
        'adjustment_direction',
        'transfer_group_id',
        'reconciled',
        'reconciled_at',
        'observations',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'movement_date' => 'date',
        'reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
    ];

    public function statementLines(): HasMany
    {
        return $this->hasMany(
            BankStatementLine::class,
            'matched_bank_movement_id'
        );
    }    

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function apiTransactions()
    {
        return $this->hasMany(BankApiTransaction::class);
    }

    public function isIncome(): bool
    {
        return in_array($this->type, [
            'income',
            'transfer_in',
        ]) || (
            $this->type === 'adjustment'
            && $this->adjustment_direction === 'positive'
        );
    }

    public function isExpense(): bool
    {
        return in_array($this->type, [
            'expense',
            'transfer_out',
        ]) || (
            $this->type === 'adjustment'
            && $this->adjustment_direction === 'negative'
        );
    }


    protected static function booted(): void
    {
        static::updating(function (BankMovement $movement) {
            $protectedFields = [
                'bank_account_id',
                'type',
                'reference',
                'description',
                'amount',
                'movement_date',
                'payment_id',
                'reference_payment_id',
                'source',
                'transfer_group_id',
            ];

            $financialChanges = collect($protectedFields)
                ->filter(fn ($field) => $movement->isDirty($field));

            if ($financialChanges->isEmpty()) {
                return;
            }

            $isProtected =
                $movement->reconciled ||
                !empty($movement->payment_id) ||
                !empty($movement->reference_payment_id) ||
                in_array($movement->type, ['transfer_in', 'transfer_out'], true) ||
                !empty($movement->transfer_group_id);

            if ($isProtected) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'movement' => 'Este movimento está protegido. Não é permitido alterar os seus dados financeiros.',
                ]);
            }
        });

        static::deleting(function (BankMovement $movement) {
            $isProtected =
                $movement->reconciled ||
                !empty($movement->payment_id) ||
                !empty($movement->reference_payment_id) ||
                in_array($movement->type, ['transfer_in', 'transfer_out'], true) ||
                !empty($movement->transfer_group_id);

            if ($isProtected) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'movement' => 'Este movimento não pode ser eliminado porque está associado a um pagamento, transferência ou reconciliação.',
                ]);
            }
        });
    }

}
