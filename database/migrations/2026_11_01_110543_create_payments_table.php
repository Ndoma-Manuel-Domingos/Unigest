<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->restrictOnDelete();

            $table->string('payment_number', 50);

            $table->date('payment_date');

            $table->decimal('amount', 15, 2);

            $table->string('currency', 3)->default('AOA');

            $table->string('payment_method', 30);
            // cash, bank_transfer, multicaixa, pos, reference, other

            $table->string('transaction_reference')->nullable();

            $table->string('status', 30)->default('confirmed');
            // pending, confirmed, cancelled, reversed

            $table->text('observations')->nullable();

            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'payment_number'],
                'payments_tenant_number_unique'
            );

            $table->index(['tenant_id', 'invoice_id']);
            $table->index(['tenant_id', 'payment_date']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
