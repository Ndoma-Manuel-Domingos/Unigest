<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('enrollment_id')
                ->constrained('enrollments')
                ->cascadeOnDelete();

            $table->foreignId('curriculum_subject_id')
                ->constrained('curriculum_subjects')
                ->restrictOnDelete();

            $table->date('enrollment_date');

            $table->string('status', 30)->default('enrolled');

            $table->decimal('final_grade', 5, 2)->nullable();

            $table->text('observations')->nullable();

            $table->foreignId('registered_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'enrollment_id', 'curriculum_subject_id'],
                'subject_enrollments_unique'
            );

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'curriculum_subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_enrollments');
    }
};
