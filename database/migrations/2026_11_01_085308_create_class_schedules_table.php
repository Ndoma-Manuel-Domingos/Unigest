<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('academic_class_id')
                ->constrained('academic_classes')
                ->cascadeOnDelete();

            $table->foreignId('curriculum_subject_id')
                ->constrained('curriculum_subjects')
                ->restrictOnDelete();

            $table->foreignId('teacher_id')
                ->constrained('teachers')
                ->restrictOnDelete();

            $table->foreignId('room_id')
                ->nullable()
                ->constrained('rooms')
                ->nullOnDelete();

            $table->unsignedTinyInteger('day_of_week');

            $table->time('start_time');

            $table->time('end_time');

            $table->string('shift', 30)
                ->nullable();

            $table->string('status', 30)
                ->default('active');

            $table->date('starts_at')
                ->nullable();

            $table->date('ends_at')
                ->nullable();

            $table->text('observations')
                ->nullable();

            $table->timestamps();

            $table->index([
                'tenant_id',
                'academic_class_id',
                'day_of_week',
            ]);

            $table->index([
                'tenant_id',
                'teacher_id',
                'day_of_week',
            ]);

            $table->index([
                'tenant_id',
                'room_id',
                'day_of_week',
            ]);

            $table->index([
                'tenant_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
