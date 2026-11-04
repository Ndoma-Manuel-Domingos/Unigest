<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('treasury_daily_closures', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->date('closure_date');

            $table->decimal('opening_balance', 15, 2)
                ->default(0);

            $table->decimal('total_income', 15, 2)
                ->default(0);

            $table->decimal('total_expense', 15, 2)
                ->default(0);

            $table->decimal('expected_balance', 15, 2)
                ->default(0);

            $table->decimal('counted_balance', 15, 2)
                ->nullable();

            $table->decimal('difference', 15, 2)
                ->nullable();

            $table->enum('status', [
                'open',
                'closed',
            ])->default('open');

            $table->foreignId('closed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('closed_at')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'closure_date',
            ]);

            $table->index([
                'tenant_id',
                'status',
            ]);

            $table->index([
                'tenant_id',
                'closure_date',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('treasury_daily_closures');
    }
};
