<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('bank_account_id')
                ->constrained('bank_accounts')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('reference', 50);

            $table->date('period_start');
            $table->date('period_end');

            $table->decimal('statement_opening_balance', 15, 2);
            $table->decimal('statement_closing_balance', 15, 2);

            $table->decimal('system_opening_balance', 15, 2)
                ->default(0);

            $table->decimal('system_closing_balance', 15, 2)
                ->default(0);

            $table->decimal('difference', 15, 2)
                ->default(0);

            $table->string('status', 20)
                ->default('draft');

            $table->timestamp('reconciled_at')
                ->nullable();

            $table->foreignId('reconciled_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'reference',
            ]);

            $table->index([
                'tenant_id',
                'bank_account_id',
                'period_start',
                'period_end',
            ], 'bank_recon_tenant_account_period_idx');

            $table->index([
                'tenant_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_reconciliations');
    }
};
