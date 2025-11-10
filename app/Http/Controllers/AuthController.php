<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman/form login.
     */
    public function showLoginForm()
    {
        // === TAMBAHAN PENTING ===
        // Jika user sudah login, lempar ke dashboard
        if (Auth::check()) {
            return Redirect::route('dashboard');
        }
        // =======================

        return view('auth.login');
    }

    /**
     * Memproses data login dari form.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return Redirect::intended(route('dashboard'));
        }

        return back()->withErrors([
            'username' => 'Username atau password tidak cocok.',
        ])->onlyInput('username');
    }

    /**
     * Memproses logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Arahkan kembali ke halaman login (bukan root)
        return Redirect::route('login');
    }
}