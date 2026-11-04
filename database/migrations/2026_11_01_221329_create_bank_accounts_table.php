<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('bank_name');
            $table->string('account_number')->nullable();
            $table->string('iban')->nullable();
            $table->string('swift')->nullable();
            $table->string('currency', 3)->default('AOA');

            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);

            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);

            $table->string('branch')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'account_number'],
                'bank_accounts_tenant_account_unique'
            );

            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
