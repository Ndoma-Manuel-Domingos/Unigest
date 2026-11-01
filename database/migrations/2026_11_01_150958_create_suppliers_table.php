<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code', 50);
            $table->string('tax_number', 50)->nullable();

            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable();

            $table->string('bank_name')->nullable();
            $table->string('bank_account', 100)->nullable();
            $table->string('iban', 100)->nullable();

            $table->text('notes')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'code'],
                'suppliers_tenant_code_unique'
            );

            $table->index(
                ['tenant_id', 'name'],
                'suppliers_tenant_name_index'
            );

            $table->index(
                ['tenant_id', 'tax_number'],
                'suppliers_tenant_tax_number_index'
            );

            $table->index(
                ['tenant_id', 'is_active'],
                'suppliers_tenant_active_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
