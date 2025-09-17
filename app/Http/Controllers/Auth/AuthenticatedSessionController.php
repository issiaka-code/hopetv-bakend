<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
            
            $request->session()->regenerate();

            // Redirection avec message de succès (évite les problèmes de lint/typage sur session()->flash)
            return redirect()->intended(RouteServiceProvider::HOME)
                ->with('status', __('auth.login_success'));
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Les erreurs de validation sont automatiquement gérées par Laravel
            // et redirigées vers la page de connexion avec les erreurs
            throw $e;
        } catch (\Exception $e) {
            // Gestion des autres erreurs inattendues
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Une erreur inattendue s\'est produite. Veuillez réessayer.']);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login')->with('status', __('auth.logout_success'));
    }
}
