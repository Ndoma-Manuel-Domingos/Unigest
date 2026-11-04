<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('petty_cash_fund_id')
                ->constrained('petty_cash_funds')
                ->restrictOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('movement_number', 50);

            $table->enum('type', [
                'expense',
                'replenishment',
                'adjustment',
            ]);

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'cancelled',
            ])->default('pending');

            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->string('document_number')->nullable();
            $table->string('supplier_name')->nullable();

            $table->date('movement_date');

            $table->string('receipt_path')->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('approval_notes')->nullable();

            $table->timestamps();

            $table->unique(['tenant_id', 'movement_number']);
            $table->index(['tenant_id', 'type', 'status']);
            $table->index(['petty_cash_fund_id', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_movements');
    }
};
