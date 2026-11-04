<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankIntegration extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'public_id',
        'tenant_id',
        'bank_account_id',
        'provider',
        'name',
        'environment',
        'status',
        'base_url',
        'webhook_url',
        'settings',
        'last_connection_at',
        'last_sync_at',
        'last_error',
        'created_by',
    ];

    protected $casts = [
        'settings' => 'array',
        'last_connection_at' => 'datetime',
        'last_sync_at' => 'datetime',
    ];

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function credentials(): HasOne
    {
        return $this->hasOne(BankIntegrationCredential::class);
    }

    public function paymentReferences(): HasMany
    {
        return $this->hasMany(BankPaymentReference::class);
    }

    public function webhookLogs(): HasMany
    {
        return $this->hasMany(BankWebhookLog::class);
    }

    public function syncLogs(): HasMany
    {
        return $this->hasMany(BankSyncLog::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BankApiTransaction::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
