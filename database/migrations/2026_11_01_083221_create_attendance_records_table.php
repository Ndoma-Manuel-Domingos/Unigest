<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('class_session_id')
                ->constrained('class_sessions')
                ->cascadeOnDelete();

            $table->foreignId('subject_enrollment_id')
                ->constrained('subject_enrollments')
                ->cascadeOnDelete();

            $table->string('status', 30)->default('absent');

            $table->time('arrival_time')->nullable();

            $table->text('justification')->nullable();

            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique([
                'tenant_id',
                'class_session_id',
                'subject_enrollment_id',
            ], 'class_sessions_tenant_subject_idx');

            $table->index([
                'tenant_id',
                'subject_enrollment_id',
            ]);

            $table->index([
                'tenant_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
