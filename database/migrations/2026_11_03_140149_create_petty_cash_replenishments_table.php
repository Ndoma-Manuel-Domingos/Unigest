<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_replenishments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('petty_cash_fund_id')
                ->constrained('petty_cash_funds')
                ->restrictOnDelete();

            $table->foreignId('requested_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('movement_id')
                ->nullable()
                ->constrained('petty_cash_movements')
                ->nullOnDelete();

            $table->string('request_number', 50);
            $table->decimal('requested_amount', 15, 2);
            $table->decimal('approved_amount', 15, 2)->nullable();

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
                'paid',
                'cancelled',
            ])->default('pending');

            $table->date('request_date');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->text('reason');
            $table->text('review_notes')->nullable();

            $table->timestamps();

            $table->unique(['tenant_id', 'request_number']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_replenishments');
    }
};
