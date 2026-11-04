<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory, BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'student_id',
        'enrollment_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'subtotal',
        'discount',
        'penalty',
        'total',
        'amount_paid',
        'balance',
        'currency',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'penalty' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmedPayments(): HasMany
    {
        return $this->hasMany(Payment::class)
            ->where('status', 'confirmed');
    }

    /**
     * Calcula o total da factura com base nas linhas,
     * descontos e multas.
     */
    public function calculateTotal(): float
    {
        $subtotal = (float) $this->items()
            ->sum('total');

        $discount = (float) $this->discount;
        $penalty = (float) $this->penalty;

        return max(0, round($subtotal - $discount + $penalty, 2));
    }

    /**
     * Actualiza os valores financeiros da factura.
     */
    public function recalculateFinancials(): void
    {
        $this->loadMissing('items');

        $subtotal = (float) $this->items->sum(
            fn ($item) => (float) $item->total
        );

        $total = max(
            0,
            round(
                $subtotal
                - (float) $this->discount
                + (float) $this->penalty,
                2
            )
        );

        $amountPaid = (float) $this->confirmedPayments()
            ->sum('amount');

        $balance = max(0, round($total - $amountPaid, 2));

        $this->subtotal = round($subtotal, 2);
        $this->total = $total;
        $this->amount_paid = round($amountPaid, 2);
        $this->balance = $balance;

        if (!in_array($this->status, ['draft', 'cancelled'], true)) {
            if ($balance <= 0) {
                $this->status = 'paid';
            } elseif ($amountPaid > 0) {
                $this->status = 'partially_paid';
            } elseif ($this->due_date && $this->due_date->isPast()) {
                $this->status = 'overdue';
            } else {
                $this->status = 'issued';
            }
        }

        $this->save();
    }

    public function isPaid(): bool
    {
        return (float) $this->balance <= 0
            && $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->balance > 0
            && $this->due_date
            && $this->due_date->lt(today())
            && !in_array($this->status, ['draft', 'cancelled'], true);
    }

    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', [
            'issued',
            'partially_paid',
            'overdue',
        ]);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function bankPaymentReferences()
    {
        return $this->hasMany(BankPaymentReference::class);
    }

    public function studentScholarship()
    {
        return $this->belongsTo(StudentScholarship::class);
    }
}
