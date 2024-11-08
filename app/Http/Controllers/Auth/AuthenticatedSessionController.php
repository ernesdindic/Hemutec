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

    $user = Auth::user();

    // Provjera licence
    if ($user && is_null($user->licence)) {
        Auth::logout();

        return redirect()->route('login')->with('error', 'Sie haben keine gültige Lizenz. Bitte kontaktieren Sie den Administrator.');
    }

    $request->session()->regenerate();

    return redirect()->intended(route('dashboard', absolute: false));
}

/* public function assignLicenseToUser($userId)
    {
        $user = User::find($userId);

        if ($user && is_null($user->licence)) {
            $user->licence = (string) Str::uuid();
            $user->save();
            return redirect()->back()->with('status', 'Die Lizenz wurde erfolgreich dem Benutzer zugewiesen.');
        }

        return redirect()->back()->with('status', 'Korisnik već ima licencu ili ne postoji.');
    }
 */

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::logout(); // Uništi sesiju

        $request->session()->invalidate(); // Uništi sve sesijske podatke

        $request->session()->regenerateToken(); // Regeneriši CSRF token

        return redirect('/login'); // Preusmeri na stranicu za prijavu
    }
}
