<?php

namespace App\Models\Concerns;

use App\Models\University;
use App\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Auth\Access\AuthorizationException;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {

            $tenantId = app(TenantContext::class)->id();

            if (!$tenantId) {
                $builder->whereRaw('1 = 0');
                return;
            }

            $builder->where(
                $builder->getModel()->qualifyColumn('tenant_id'),
                $tenantId
            );
        });

        static::creating(function ($model) {

            $tenantId = app(TenantContext::class)->id();

            if (!$tenantId) {
                throw new AuthorizationException(
                    'Não existe universidade activa no contexto.'
                );
            }

            if ($model->tenant_id &&
                (int) $model->tenant_id !== $tenantId) {

                throw new AuthorizationException(
                    'Não pode criar registos noutra universidade.'
                );
            }

            $model->tenant_id = $tenantId;
        });

        static::updating(function ($model) {

            if ($model->isDirty('tenant_id')) {
                throw new AuthorizationException(
                    'Não é permitido alterar a universidade de um registo.'
                );
            }
        });
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(
            University::class,
            'tenant_id'
        );
    }
}