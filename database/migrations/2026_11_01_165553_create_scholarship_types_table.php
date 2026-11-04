<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_types', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->string('name', 150);
            $table->string('code', 50);
            $table->text('description')->nullable();

            $table->string('type', 20); // percentage ou fixed
            $table->decimal('value', 15, 2);

            $table->decimal('maximum_amount', 15, 2)->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'code'],
                'scholarship_types_tenant_code_unique'
            );

            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scholarship_types');
    }
};
