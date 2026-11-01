<?php

namespace App\Tenancy;

use App\Models\University;

class TenantContext
{
    private ?University $tenant = null;

    public function set(University $tenant): void
    {
        $this->tenant = $tenant;
    }

    public function get(): ?University
    {
        return $this->tenant;
    }

    public function id(): ?int
    {
        return $this->tenant?->id;
    }

    public function hasTenant(): bool
    {
        return $this->tenant !== null;
    }

    public function clear(): void
    {
        $this->tenant = null;
    }
}