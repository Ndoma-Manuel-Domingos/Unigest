<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('cash_register_id')
                ->constrained('cash_registers')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('payment_id')
                ->nullable()
                ->constrained('payments')
                ->restrictOnDelete()
                ->unique();

            $table->foreignId('reference_payment_id')
                ->nullable()
                ->constrained('payments')
                ->nullOnDelete();

            $table->string('type', 20); // income, expense
            $table->string('source', 30)->default('manual'); // manual, payment

            $table->string('description');
            $table->decimal('amount', 15, 2);
            $table->timestamp('movement_date');

            $table->text('observations')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'cash_register_id']);
            $table->index(['tenant_id', 'movement_date']);
            $table->index(['tenant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_movements');
    }
};
