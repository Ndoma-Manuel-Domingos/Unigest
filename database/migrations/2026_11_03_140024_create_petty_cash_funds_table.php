<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petty_cash_funds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('responsible_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('name');
            $table->string('code', 50);
            $table->string('currency', 3)->default('AOA');

            $table->decimal('authorized_amount', 15, 2);
            $table->decimal('current_balance', 15, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'responsible_user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petty_cash_funds');
    }
};
