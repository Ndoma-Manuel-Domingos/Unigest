<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_counts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('cash_register_id')
                ->constrained('cash_registers')
                ->restrictOnDelete();

            $table->foreignId('counted_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('reference', 50);
            $table->json('denominations');

            $table->decimal('expected_balance', 15, 2);
            $table->decimal('counted_balance', 15, 2);
            $table->decimal('difference', 15, 2);

            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');

            $table->timestamp('counted_at');
            $table->timestamp('reviewed_at')->nullable();

            $table->text('notes')->nullable();
            $table->text('review_notes')->nullable();

            $table->timestamps();

            $table->unique(['tenant_id', 'reference']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'counted_at']);
            $table->index(['cash_register_id', 'counted_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_counts');
    }
};
