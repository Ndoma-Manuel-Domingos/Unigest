<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('expense_id')
                ->constrained('expenses')
                ->cascadeOnDelete();

            $table->foreignId('cash_register_id')
                ->nullable()
                ->constrained('cash_registers')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('cash_movement_id')
                ->nullable()
                ->constrained('cash_movements')
                ->nullOnDelete();

            $table->string('payment_number', 50);
            $table->timestamp('payment_date');

            $table->decimal('amount', 15, 2);
            $table->string('currency', 3)->default('AOA');

            $table->string('payment_method', 30);
            $table->string('transaction_reference', 100)->nullable();

            $table->string('status', 20)->default('confirmed');
            $table->text('observations')->nullable();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'payment_number'],
                'expense_payments_tenant_number_unique'
            );

            $table->index(['tenant_id', 'expense_id']);
            $table->index(['tenant_id', 'payment_date']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_payments');
    }
};
