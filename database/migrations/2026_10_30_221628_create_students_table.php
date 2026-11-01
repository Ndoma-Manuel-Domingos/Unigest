<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->string('student_number', 50);
            $table->string('nif', 30)->nullable();

            $table->string('document_type', 30)->nullable();
            $table->string('document_number', 50)->nullable();

            $table->string('name', 200);
            $table->string('gender', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('birth_place', 150)->nullable();
            $table->string('nationality', 100)->default('Angolana');

            $table->string('marital_status', 30)->nullable();

            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('alternative_phone', 30)->nullable();

            $table->string('province', 100)->nullable();
            $table->string('municipality', 100)->nullable();
            $table->string('address')->nullable();

            $table->string('guardian_name', 200)->nullable();
            $table->string('guardian_phone', 30)->nullable();
            $table->string('guardian_relationship', 50)->nullable();

            $table->string('photo')->nullable();
            $table->text('observations')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(['tenant_id', 'student_number']);
            $table->unique(['tenant_id', 'nif']);
            $table->unique(['tenant_id', 'document_type', 'document_number']);

            $table->index(['tenant_id', 'name']);
            $table->index(['tenant_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
