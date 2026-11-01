<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_classes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->restrictOnDelete();

            $table->foreignId('curriculum_id')
                ->nullable()
                ->constrained('curricula')
                ->restrictOnDelete();

            $table->string('name', 150);

            $table->string('code', 50);

            $table->unsignedSmallInteger('year_number');

            $table->unsignedSmallInteger('semester_number');

            $table->enum('shift', [
                'manha',
                'tarde',
                'noite',
                'integral',
                'pos_laboral',
                'outro',
            ])->default('manha');

            $table->enum('study_regime', [
                'presencial',
                'online',
                'hibrido',
            ])->default('presencial');

            $table->unsignedInteger('capacity')->default(40);

            $table->date('start_date')->nullable();

            $table->date('end_date')->nullable();

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'code'],
                'academic_classes_tenant_code_unique'
            );

            $table->index(
                ['tenant_id', 'academic_year_id'],
                'academic_classes_tenant_year_index'
            );

            $table->index(
                ['tenant_id', 'course_id'],
                'academic_classes_tenant_course_index'
            );

            $table->index(
                ['tenant_id', 'is_active'],
                'academic_classes_tenant_active_index'
            );

            $table->index(
                ['academic_year_id', 'year_number', 'semester_number'],
                'academic_classes_period_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_classes');
    }
};
