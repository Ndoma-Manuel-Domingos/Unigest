<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\StudentScholarship;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ScholarshipDiscountService
{
    /**
     * Verifica se a bolsa está válida para o estudante
     * e para a data da factura.
     */
    public function validateForInvoice(StudentScholarship $scholarship, Invoice $invoice): void 
    {
        if ($scholarship->tenant_id !== $invoice->tenant_id) {
            throw ValidationException::withMessages([
                'student_scholarship_id' => 'A bolsa não pertence à mesma universidade da factura.',
            ]);
        }

        if ($scholarship->student_id !== $invoice->student_id) {
            throw ValidationException::withMessages([
                'student_scholarship_id' => 'A bolsa seleccionada não pertence ao estudante da factura.',
            ]);
        }

        if ($scholarship->status !== 'approved') {
            throw ValidationException::withMessages([
                'student_scholarship_id' => 'A bolsa seleccionada não está aprovada.',
            ]);
        }

        $date = $invoice->invoice_date ?? $invoice->due_date ?? now();

        $date = Carbon::parse($date);

        if ($scholarship->start_date &&$date->lt(Carbon::parse($scholarship->start_date))) {
            throw ValidationException::withMessages([
                'student_scholarship_id' => 'A bolsa ainda não é válida na data da factura.',
            ]);
        }

        if (
            $scholarship->end_date &&
            $date->gt(Carbon::parse($scholarship->end_date))
        ) {
            throw ValidationException::withMessages([
                'student_scholarship_id' => 'A validade da bolsa terminou.',
            ]);
        }

        if (
            $scholarship->academic_year_id &&
            $invoice->enrollment &&
            $invoice->enrollment->academic_year_id &&
            $scholarship->academic_year_id !==
                $invoice->enrollment->academic_year_id
        ) {
            throw ValidationException::withMessages([
                'student_scholarship_id' =>
                    'A bolsa não corresponde ao ano académico da matrícula.',
            ]);
        }
    }

    /**
     * Calcula o desconto da bolsa sobre o subtotal da factura.
     */
    public function calculate( StudentScholarship $scholarship, float $subtotal): float 
    {
        if ($subtotal <= 0) {
            return 0;
        }

        $type = $scholarship->scholarshipType;

        if (!$type || !$type->is_active) {
            return 0;
        }

        $value = (float) (
            $scholarship->approved_value
            ?? $type->value
        );

        if ($value <= 0) {
            return 0;
        }

        if ($type->type === 'percentage') {
            $discount = $subtotal * ($value / 100);
        } else {
            $discount = $value;
        }

        if (
            $type->maximum_amount !== null &&
            $type->maximum_amount >= 0
        ) {
            $discount = min(
                $discount,
                (float) $type->maximum_amount
            );
        }

        return round(
            min($discount, $subtotal),
            2
        );
    }

    /**
     * Aplica a bolsa a uma factura.
     */
    public function apply( Invoice $invoice, StudentScholarship $scholarship): Invoice 
    {
        $this->validateForInvoice(
            $scholarship,
            $invoice
        );

        if (
            $invoice->status !== 'draft'
        ) {
            throw ValidationException::withMessages([
                'invoice' => 'A bolsa só pode ser aplicada a uma factura em rascunho.',
            ]);
        }

        if (
            $invoice->payments()
                ->where('status', 'confirmed')
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'invoice' => 'Não é possível aplicar uma bolsa a uma factura que já possui pagamentos.',
            ]);
        }

        $subtotal = (float) $invoice->subtotal;

        $discount = $this->calculate(
            $scholarship,
            $subtotal
        );

        $invoice->student_scholarship_id = $scholarship->id;
        $invoice->discount = $discount;

        $invoice->recalculateFinancials();

        return $invoice->fresh();
    }

    /**
     * Remove a bolsa da factura.
     */
    public function remove(Invoice $invoice): Invoice
    {
        if ($invoice->status !== 'draft') {
            throw ValidationException::withMessages([
                'invoice' => 'A bolsa só pode ser removida de uma factura em rascunho.',
            ]);
        }

        if (
            $invoice->payments()
                ->where('status', 'confirmed')
                ->exists()
        ) {
            throw ValidationException::withMessages([
                'invoice' => 'Não é possível alterar o desconto de uma factura que já possui pagamentos.',
            ]);
        }

        $invoice->student_scholarship_id = null;
        $invoice->discount = 0;

        $invoice->recalculateFinancials();

        return $invoice->fresh();
    }
}
