<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_integrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id')->unique();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('bank_account_id')
                ->constrained('bank_accounts')
                ->cascadeOnDelete();

            $table->string('provider', 80);
            $table->string('name', 150);
            $table->string('environment', 20)->default('sandbox');
            $table->string('status', 20)->default('draft');

            $table->string('base_url')->nullable();
            $table->string('webhook_url')->nullable();

            $table->json('settings')->nullable();

            $table->timestamp('last_connection_at')->nullable();
            $table->timestamp('last_sync_at')->nullable();
            $table->text('last_error')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'bank_account_id', 'provider'],
                'bank_integrations_account_provider_unique'
            );

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('bank_integration_credentials', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('bank_integration_id')
                ->unique()
                ->constrained('bank_integrations')
                ->cascadeOnDelete();

            $table->json('credentials');
            $table->timestamp('rotated_at')->nullable();

            $table->timestamps();

            $table->index('tenant_id');
        });

        Schema::create('bank_payment_references', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('bank_integration_id')
                ->constrained('bank_integrations')
                ->cascadeOnDelete();

            $table->foreignId('invoice_id')
                ->constrained('invoices')
                ->cascadeOnDelete();

            $table->string('reference', 100);
            $table->string('external_reference', 150)->nullable();

            $table->decimal('amount', 15, 2);
            $table->char('currency', 3)->default('AOA');

            $table->string('status', 20)->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'reference'],
                'bank_payment_references_tenant_reference_unique'
            );

            $table->index(['tenant_id', 'invoice_id']);
            $table->index(['tenant_id', 'status']);
        });

        Schema::create('bank_webhook_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('bank_integration_id')
                ->constrained('bank_integrations')
                ->cascadeOnDelete();

            $table->string('external_event_id', 150)->nullable();
            $table->string('event_type', 100)->nullable();

            $table->json('payload');
            $table->boolean('signature_valid')->default(false);

            $table->string('status', 20)->default('received');
            $table->unsignedSmallInteger('attempts')->default(0);

            $table->timestamp('received_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();

            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'bank_integration_id', 'external_event_id'],
                'bank_webhook_events_unique'
            );

            $table->index(['tenant_id', 'status']);
        });

        Schema::create('bank_sync_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('bank_integration_id')
                ->constrained('bank_integrations')
                ->cascadeOnDelete();

            $table->string('status', 20)->default('running');

            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();

            $table->unsignedInteger('records_found')->default(0);
            $table->unsignedInteger('records_imported')->default(0);
            $table->unsignedInteger('records_ignored')->default(0);

            $table->string('cursor')->nullable();
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->index(['tenant_id', 'status']);
            $table->index(['bank_integration_id', 'started_at']);
        });

        Schema::create('bank_api_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('tenant_id')
                ->constrained('universities')
                ->cascadeOnDelete();

            $table->foreignId('bank_integration_id')
                ->constrained('bank_integrations')
                ->cascadeOnDelete();

            $table->foreignId('bank_account_id')
                ->constrained('bank_accounts')
                ->cascadeOnDelete();

            $table->foreignId('bank_movement_id')
                ->nullable()
                ->constrained('bank_movements')
                ->nullOnDelete();

            $table->string('external_transaction_id', 150);
            $table->string('reference', 150)->nullable();
            $table->string('description')->nullable();

            $table->string('type', 20);
            $table->decimal('amount', 15, 2);
            $table->char('currency', 3)->default('AOA');

            $table->timestamp('occurred_at');
            $table->string('status', 20)->default('pending');

            $table->json('raw_payload')->nullable();

            $table->timestamps();

            $table->unique(
                ['tenant_id', 'bank_integration_id', 'external_transaction_id'],
                'bank_api_transactions_external_unique'
            );

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'reference']);
            $table->index(['bank_account_id', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_api_transactions');
        Schema::dropIfExists('bank_sync_logs');
        Schema::dropIfExists('bank_webhook_logs');
        Schema::dropIfExists('bank_payment_references');
        Schema::dropIfExists('bank_integration_credentials');
        Schema::dropIfExists('bank_integrations');
    }
};
