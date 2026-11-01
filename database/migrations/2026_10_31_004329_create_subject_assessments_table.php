<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subject_assessments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('academic_class_id')
                ->constrained('academic_classes')
                ->restrictOnDelete();

            $table->foreignId('curriculum_subject_id')
                ->constrained('curriculum_subjects')
                ->restrictOnDelete();

            $table->string('title', 150);
            $table->string('type', 30);
            $table->date('assessment_date');

            $table->decimal('weight', 5, 2);
            $table->decimal('max_grade', 5, 2)->default(20);

            $table->text('description')->nullable();

            $table->boolean('is_published')->default(false);

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(
                ['tenant_id', 'academic_class_id', 'curriculum_subject_id'],
                'assessments_class_subject_index'
            );

            $table->index(['tenant_id', 'assessment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subject_assessments');
    }
};
