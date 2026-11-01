<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curricula', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->restrictOnDelete();

            $table->string('name', 200);

            $table->string('version', 50)->nullable();

            $table->date('effective_from')->nullable();

            $table->date('effective_to')->nullable();

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'course_id', 'name'],
                'curricula_tenant_course_name_unique'
            );

            $table->index(
                ['tenant_id', 'course_id'],
                'curricula_tenant_course_index'
            );

            $table->index(
                ['tenant_id', 'is_active'],
                'curricula_tenant_active_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curricula');
    }
};
