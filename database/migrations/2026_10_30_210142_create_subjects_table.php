<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained('departments')
                ->nullOnDelete();

            $table->string('name', 200);
            $table->string('code', 50);

            $table->text('description')->nullable();

            $table->enum('type', [
                'obrigatoria',
                'opcional',
                'electiva',
                'livre',
            ])->default('obrigatoria');

            $table->unsignedSmallInteger('year_number')->nullable();
            $table->unsignedSmallInteger('semester_number')->nullable();

            $table->unsignedSmallInteger('credits')->default(0);

            $table->unsignedSmallInteger('theoretical_hours')->default(0);
            $table->unsignedSmallInteger('practical_hours')->default(0);
            $table->unsignedSmallInteger('laboratory_hours')->default(0);

            $table->unsignedSmallInteger('total_hours')->default(0);

            $table->boolean('requires_prerequisite')->default(false);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'code'],
                'subjects_tenant_code_unique'
            );

            $table->unique(
                ['tenant_id', 'name'],
                'subjects_tenant_name_unique'
            );

            $table->index(['tenant_id', 'department_id']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
