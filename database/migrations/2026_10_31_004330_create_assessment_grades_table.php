<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessment_grades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('subject_assessment_id')
                ->constrained('subject_assessments')
                ->cascadeOnDelete();

            $table->foreignId('subject_enrollment_id')
                ->constrained('subject_enrollments')
                ->cascadeOnDelete();

            $table->decimal('grade', 5, 2)->nullable();

            $table->text('observations')->nullable();

            $table->foreignId('entered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'subject_assessment_id', 'subject_enrollment_id'],
                'assessment_grade_unique'
            );

            $table->index(['tenant_id', 'subject_enrollment_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_grades');
    }
};
