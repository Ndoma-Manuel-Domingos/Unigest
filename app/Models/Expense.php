<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'supplier_id',
        'category_id',
        'created_by',
        'approved_by',
        'expense_number',
        'description',
        'reference',
        'expense_date',
        'due_date',
        'approved_at',
        'paid_at',
        'amount',
        'paid_amount',
        'balance',
        'currency',
        'payment_method',
        'status',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',

        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    protected $appends = [
        'is_paid',
        'is_overdue',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            ExpenseCategory::class,
            'category_id'
        );
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'approved_by'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePartiallyPaid($query)
    {
        return $query->where('status', 'partially_paid');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', [
            'approved',
            'partially_paid',
        ]);
    }

    public function scopeOverdue($query)
    {
        return $query
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', now()->toDateString())
            ->whereIn('status', [
                'approved',
                'partially_paid',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Financial Helpers
    |--------------------------------------------------------------------------
    */

    public function recalculateBalance(): self
    {
        $amount = (float) $this->amount;
        $paidAmount = (float) $this->paid_amount;

        $balance = max($amount - $paidAmount, 0);

        $this->balance = $balance;

        return $this;
    }

    public function recalculateFinancials(): self
    {
        $this->recalculateBalance();

        if ($this->status === 'cancelled') {
            return $this;
        }

        $amount = (float) $this->amount;
        $paidAmount = (float) $this->paid_amount;

        if ($amount <= 0) {
            $this->balance = 0;

            return $this;
        }

        if ($paidAmount <= 0) {
            if (
                in_array($this->status, [
                    'partially_paid',
                    'paid',
                ], true)
            ) {
                $this->status = 'approved';
            }

            return $this;
        }

        if ($paidAmount >= $amount) {
            $this->paid_amount = $amount;
            $this->balance = 0;
            $this->status = 'paid';

            if (!$this->paid_at) {
                $this->paid_at = now();
            }

            return $this;
        }

        $this->balance = $amount - $paidAmount;
        $this->status = 'partially_paid';

        return $this;
    }

    /*
    |--------------------------------------------------------------------------
    | Status Helpers
    |--------------------------------------------------------------------------
    */

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isPartiallyPaid(): bool
    {
        return $this->status === 'partially_paid';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    public function canBeApproved(): bool
    {
        return in_array($this->status, [
            'draft',
            'pending',
        ], true);
    }

    public function canBePaid(): bool
    {
        return in_array($this->status, [
            'approved',
            'partially_paid',
        ], true);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            'draft',
            'pending',
            'approved',
        ], true) && (float) $this->paid_amount <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'paid'
            || (float) $this->balance <= 0 && (float) $this->amount > 0;
    }

    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date) {
            return false;
        }

        if ($this->isPaid || $this->isCancelled) {
            return false;
        }

        return $this->due_date->isBefore(
            Carbon::today()
        );
    }

    public function payments()
    {
        return $this->hasMany(ExpensePayment::class)
            ->orderByDesc('payment_date');
    }
}
