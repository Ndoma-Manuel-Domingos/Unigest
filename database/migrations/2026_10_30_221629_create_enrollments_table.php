<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('student_id')
                ->constrained('students')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->restrictOnDelete();

            $table->foreignId('academic_class_id')
                ->constrained('academic_classes')
                ->restrictOnDelete();

            $table->foreignId('curriculum_id')
                ->nullable()
                ->constrained('curricula')
                ->restrictOnDelete();

            $table->string('enrollment_number', 60);

            $table->date('enrollment_date');

            $table->unsignedSmallInteger('year_number');
            $table->unsignedSmallInteger('semester_number')->nullable();

            $table->string('enrollment_type', 30)->default('regular');

            $table->string('status', 30)->default('pending');

            $table->text('observations')->nullable();

            $table->foreignId('registered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['tenant_id', 'enrollment_number']);

            // Um estudante não pode ter duas matrículas no mesmo ano lectivo.
            $table->unique([
                'tenant_id',
                'student_id',
                'academic_year_id'
            ], 'enrollments_student_year_unique');

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'academic_class_id']);
            $table->index(['tenant_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
