<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_tariffs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('fee_concept_id')
                ->constrained('fee_concepts')
                ->restrictOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->restrictOnDelete();

            $table->foreignId('course_id')
                ->nullable()
                ->constrained('courses')
                ->restrictOnDelete();

            $table->decimal('amount', 15, 2);

            $table->string('frequency', 30)->default('once');
            // once, monthly, semesterly, annually

            $table->unsignedTinyInteger('due_day')->nullable();

            $table->string('currency', 3)->default('AOA');

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index([
                'tenant_id',
                'academic_year_id',
                'course_id',
            ]);

            $table->index([
                'tenant_id',
                'fee_concept_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_tariffs');
    }
};
