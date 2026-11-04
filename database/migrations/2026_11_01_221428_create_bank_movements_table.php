<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_movements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('bank_account_id')
                ->constrained('bank_accounts')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->enum('type', [
                'income',
                'expense',
                'transfer_in',
                'transfer_out',
                'adjustment',
            ]);

            $table->string('reference')->nullable();
            $table->string('description');

            $table->decimal('amount', 15, 2);

            $table->date('movement_date');

            $table->string('document_number')->nullable();
            $table->string('transaction_reference')->nullable();

            $table->boolean('reconciled')->default(false);
            $table->timestamp('reconciled_at')->nullable();

            $table->text('observations')->nullable();

            $table->timestamps();

            $table->index([
                'tenant_id',
                'bank_account_id',
                'movement_date',
            ]);

            $table->index([
                'tenant_id',
                'reconciled',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_movements');
    }
};
