<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            $table->foreignId('enrollment_id')
                ->nullable()
                ->constrained('enrollments')
                ->restrictOnDelete();

            $table->string('invoice_number', 50);

            $table->date('issue_date');
            $table->date('due_date')->nullable();

            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('penalty', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);

            $table->string('currency', 3)->default('AOA');

            $table->string('status', 30)->default('draft');
            // draft, issued, partially_paid, paid, overdue, cancelled

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'invoice_number'],
                'invoices_tenant_number_unique'
            );

            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'issue_date']);
            $table->index(['tenant_id', 'due_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
