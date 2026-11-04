<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('module', 80);
            $table->string('event', 80);

            $table->string('auditable_type', 150)->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();

            $table->string('description', 500);

            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'module', 'created_at']);
            $table->index(['tenant_id', 'user_id', 'created_at']);
            $table->index(['auditable_type', 'auditable_id']);
            $table->index(['tenant_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_audit_logs');
    }
};
