<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('faculty_id')
                ->constrained('faculties')
                ->restrictOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('name', 200);
            $table->string('code', 50);
            $table->string('acronym', 30)->nullable();

            $table->enum('degree_type', [
                'licenciatura',
                'bacharelato',
                'mestrado',
                'doutoramento',
                'pos_graduacao',
                'curso_tecnico',
                'outro',
            ]);

            $table->unsignedSmallInteger('duration_years')->nullable();
            $table->unsignedSmallInteger('duration_semesters')->nullable();

            $table->enum('study_regime', [
                'presencial',
                'pos_laboral',
                'online',
                'hibrido',
            ])->default('presencial');

            $table->text('description')->nullable();

            $table->boolean('is_accredited')->default(false);
            $table->string('accreditation_number', 100)->nullable();
            $table->date('accreditation_date')->nullable();
            $table->date('accreditation_expiry')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'code'],
                'courses_tenant_code_unique'
            );

            $table->unique(
                ['tenant_id', 'name'],
                'courses_tenant_name_unique'
            );

            $table->index(['tenant_id', 'faculty_id']);
            $table->index(['tenant_id', 'department_id']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
