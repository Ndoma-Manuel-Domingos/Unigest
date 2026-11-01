<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curriculum_subjects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('curriculum_id')
                ->constrained('curricula')
                ->cascadeOnDelete();

            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->restrictOnDelete();

            $table->unsignedSmallInteger('year_number');

            $table->unsignedSmallInteger('semester_number');

            $table->enum('type', [
                'obrigatoria',
                'opcional',
                'electiva',
            ])->default('obrigatoria');

            $table->unsignedSmallInteger('credits')->default(0);

            $table->unsignedSmallInteger('theoretical_hours')->default(0);

            $table->unsignedSmallInteger('practical_hours')->default(0);

            $table->unsignedSmallInteger('laboratory_hours')->default(0);

            $table->unsignedSmallInteger('total_hours')->default(0);

            $table->unsignedSmallInteger('order_number')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['curriculum_id', 'subject_id'],
                'curriculum_subjects_curriculum_subject_unique'
            );

            $table->index(
                ['tenant_id', 'curriculum_id'],
                'curriculum_subjects_tenant_curriculum_index'
            );

            $table->index(
                ['curriculum_id', 'year_number', 'semester_number'],
                'curriculum_subjects_period_index'
            );

            $table->index(
                ['tenant_id', 'is_active'],
                'curriculum_subjects_tenant_active_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curriculum_subjects');
    }
};
