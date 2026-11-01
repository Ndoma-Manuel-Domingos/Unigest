<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Tenant
            |--------------------------------------------------------------------------
            */
            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Relações
            |--------------------------------------------------------------------------
            */
            $table->foreignId('supplier_id')
                ->nullable()
                ->constrained('suppliers')
                ->nullOnDelete();

            $table->foreignId('category_id')
                ->constrained('expense_categories')
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Identificação
            |--------------------------------------------------------------------------
            */
            $table->string('expense_number', 50);

            $table->string('description');

            $table->string('reference', 100)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Datas
            |--------------------------------------------------------------------------
            */
            $table->date('expense_date');

            $table->date('due_date')
                ->nullable();

            $table->timestamp('approved_at')
                ->nullable();

            $table->timestamp('paid_at')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Valores
            |--------------------------------------------------------------------------
            */
            $table->decimal('amount', 15, 2)
                ->default(0);

            $table->decimal('paid_amount', 15, 2)
                ->default(0);

            $table->decimal('balance', 15, 2)
                ->default(0);

            $table->string('currency', 3)
                ->default('AOA');

            /*
            |--------------------------------------------------------------------------
            | Pagamento
            |--------------------------------------------------------------------------
            */
            $table->string('payment_method', 30)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Estado
            |--------------------------------------------------------------------------
            |
            | draft
            | pending
            | approved
            | partially_paid
            | paid
            | cancelled
            |
            */
            $table->string('status', 30)
                ->default('draft');

            /*
            |--------------------------------------------------------------------------
            | Observações
            |--------------------------------------------------------------------------
            */
            $table->text('notes')
                ->nullable();

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Índices e unicidade
            |--------------------------------------------------------------------------
            */

            $table->unique(
                ['tenant_id', 'expense_number'],
                'expenses_tenant_number_unique'
            );

            $table->index(
                ['tenant_id', 'supplier_id'],
                'expenses_tenant_supplier_index'
            );

            $table->index(
                ['tenant_id', 'category_id'],
                'expenses_tenant_category_index'
            );

            $table->index(
                ['tenant_id', 'status'],
                'expenses_tenant_status_index'
            );

            $table->index(
                ['tenant_id', 'expense_date'],
                'expenses_tenant_date_index'
            );

            $table->index(
                ['tenant_id', 'due_date'],
                'expenses_tenant_due_date_index'
            );

            $table->index(
                ['tenant_id', 'created_by'],
                'expenses_tenant_created_by_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
