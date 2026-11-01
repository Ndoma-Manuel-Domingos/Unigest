<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('code', 50);

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'code'],
                'expense_categories_tenant_code_unique'
            );

            $table->index(
                ['tenant_id', 'name'],
                'expense_categories_tenant_name_index'
            );

            $table->index(
                ['tenant_id', 'is_active'],
                'expense_categories_tenant_active_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
