<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->string('name', 50);

            $table->date('start_date');

            $table->date('end_date');

            $table->boolean('is_current')->default(false);

            $table->boolean('is_active')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'name'],
                'academic_years_tenant_name_unique'
            );

            $table->index(
                ['tenant_id', 'is_active'],
                'academic_years_tenant_active_index'
            );

            $table->index(
                ['tenant_id', 'is_current'],
                'academic_years_tenant_current_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
