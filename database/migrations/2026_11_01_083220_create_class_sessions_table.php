<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_sessions', function (Blueprint $table) {
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

            $table->date('session_date');

            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            $table->string('topic')->nullable();
            $table->text('content')->nullable();

            $table->string('status', 30)->default('scheduled');

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index([
                'tenant_id',
                'academic_class_id',
                'session_date',
            ]);

            $table->index([
                'tenant_id',
                'curriculum_subject_id',
                'session_date',
            ], 'class_sessions_tenant_subject_date_idx');

            $table->index([
                'tenant_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
