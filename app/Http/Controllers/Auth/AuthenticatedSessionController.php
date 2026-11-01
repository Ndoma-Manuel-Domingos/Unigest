<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Super Administrador da plataforma
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('platform.universities.index');
        }

        // Administrador da universidade
        if ($user->hasRole('Administrador da Universidade')) {
            return redirect()->route('university.dashboard');
        }

        // Restantes utilizadores da universidade
        if ($user->tenant_id) {
            return redirect()->route('university.dashboard');
        }

        // Utilizador sem perfil ou universidade associados
        auth()->logout();

        return redirect()
            ->route('login')
            ->withErrors([
                'email' => 'A sua conta não possui um perfil ou universidade associados.',
            ]);
    }

        
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
