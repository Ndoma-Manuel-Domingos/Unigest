<?php

namespace App\Http\Middleware;

use App\Models\University;
use App\Tenancy\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenant
{
    public function __construct(private TenantContext $context) {}

    public function handle(
        Request $request,
        Closure $next
    ): Response {
        $user = $request->user();

        if (!$user) {
            abort(401);
        }

        if ($user->hasRole('Super Admin')) {
            abort(403, 'O Super Admin deve utilizar o painel da plataforma.');
        }

        if (!$user->is_active) {
            abort(403, 'A sua conta está desactivada.');
        }

        if (!$user->tenant_id) {
            abort(403, 'Utilizador sem universidade associada.');
        }

        $university = University::query()
            ->whereKey($user->tenant_id)
            ->whereIn('status', ['active', 'trial'])
            ->first();

        if (!$university) {
            abort(403, 'Universidade inactiva ou indisponível.');
        }

        $this->context->set($university);

        try {
            return $next($request);
        } finally {
            $this->context->clear();
        }
    }
}