<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('teacher_number');

            $table->string('employee_number')->nullable();

            $table->string('name');

            $table->string('gender', 20)->nullable();

            $table->date('birth_date')->nullable();

            $table->string('email')->nullable();

            $table->string('phone')->nullable();

            $table->string('academic_title')->nullable();

            $table->string('specialization')->nullable();

            $table->text('address')->nullable();

            $table->text('observations')->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'teacher_number',
            ]);

            $table->index([
                'tenant_id',
                'user_id',
            ]);

            $table->index([
                'tenant_id',
                'is_active',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
