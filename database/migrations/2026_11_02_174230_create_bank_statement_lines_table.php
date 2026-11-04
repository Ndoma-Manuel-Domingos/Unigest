<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_statement_lines', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('bank_reconciliation_id')
                ->constrained('bank_reconciliations')
                ->cascadeOnDelete();

            $table->foreignId('matched_bank_movement_id')
                ->nullable()
                ->constrained('bank_movements')
                ->nullOnDelete();

            $table->date('transaction_date');

            $table->string('description');

            $table->string('reference', 150)
                ->nullable();

            /*
             * income  = entrada
             * expense = saída
             */
            $table->string('type', 20);

            $table->decimal('amount', 15, 2);

            /*
             * pending = ainda não conciliado
             * matched = conciliado
             * ignored = ignorado
             */
            $table->string('status', 20)
                ->default('pending');

            $table->timestamp('matched_at')
                ->nullable();

            $table->foreignId('matched_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->json('raw_data')
                ->nullable();

            $table->text('observations')
                ->nullable();

            $table->timestamps();

            $table->index([
                'tenant_id',
                'bank_reconciliation_id',
            ]);

            $table->index([
                'tenant_id',
                'transaction_date',
            ]);

            $table->index([
                'tenant_id',
                'status',
            ]);

            $table->index([
                'bank_reconciliation_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_statement_lines');
    }
};
