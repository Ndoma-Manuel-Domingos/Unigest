<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankIntegrationCredential extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'bank_integration_id',
        'credentials',
        'rotated_at',
    ];

    protected $hidden = [
        'credentials',
    ];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'rotated_at' => 'datetime',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(BankIntegration::class, 'bank_integration_id');
    }
}
