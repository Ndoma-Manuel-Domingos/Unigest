<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_scholarships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('scholarship_type_id')
                ->constrained('scholarship_types')
                ->restrictOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                ->nullable()
                ->constrained('academic_years')
                ->nullOnDelete();

            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->date('start_date');
            $table->date('end_date')->nullable();

            $table->string('status', 20)->default('pending');
            // pending, approved, rejected, suspended, expired

            $table->decimal('approved_value', 15, 2)->nullable();

            $table->timestamp('approved_at')->nullable();
            $table->text('application_reason')->nullable();
            $table->text('decision_notes')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'student_id']);
            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'academic_year_id']);
            $table->index(['tenant_id', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_scholarships');
    }
};
