<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Session;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): RedirectResponse|Response
    {
        // Verifica se o usuário já está autenticado
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->hasRole('servidor')) {
            return redirect()->route('servidor.home');
        } elseif ($user->hasRole('aluno')) {
            return redirect()->route('aluno.home');
        }
        
        // Caso tenha alguma role não mapeada, redireciona para dashboard padrão
        return redirect()->route('dashboard');
    }
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

         if ($user->hasRole('servidor')) {
            // Redirecionar para a home do servidor no portal-aluno
            return redirect()->route('servidor.home');
        } else if ($user->hasRole('aluno')) {
            // Redirecionar para a home do aluno no portal-aluno 
            return redirect()->route('aluno.home');
        }

        return redirect()->intended(route('login', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('http://localhost:8001/login');
    }
}
