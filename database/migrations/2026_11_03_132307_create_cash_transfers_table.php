<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('source_cash_register_id')
                ->constrained('cash_registers')
                ->restrictOnDelete();

            $table->foreignId('destination_cash_register_id')
                ->constrained('cash_registers')
                ->restrictOnDelete();

            $table->foreignId('initiated_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('received_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('transfer_number', 50);
            $table->decimal('amount', 15, 2);

            $table->enum('status', [
                'pending',
                'received',
                'rejected',
                'cancelled',
            ])->default('pending');

            $table->timestamp('transfer_date');
            $table->timestamp('received_at')->nullable();

            $table->text('notes')->nullable();
            $table->text('receiver_notes')->nullable();

            $table->timestamps();

            $table->unique(['tenant_id', 'transfer_number']);

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'transfer_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_transfers');
    }
};
