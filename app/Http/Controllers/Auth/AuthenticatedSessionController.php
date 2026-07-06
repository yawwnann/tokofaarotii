<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\CartController;
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
        return view("auth.login");
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Ambil guest cart sebelum session di-regenerate
        $guestCart = $request->session()->get("cart", []);

        $request->authenticate();

        $user = Auth::user();

        $request->session()->regenerate();

        // Gabungkan guest cart dengan cart tersimpan di DB,
        // lalu hydrate session agar cart tidak hilang setelah login.
        if (
            in_array($user->role, ["customer", "pegawai"]) ||
            !in_array($user->role, ["admin_master", "pemilik", "kasir"])
        ) {
            CartController::hydrateFromDb($user->id, $guestCart);
        }

        // Cek Role untuk Redirection
        if (in_array($user->role, ["admin_master", "pemilik"])) {
            return redirect()->intended(route("dashboard"));
        }

        // Untuk User Biasa / Pelanggan
        return redirect()->intended(route("welcome"));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Cart sudah tersinkron ke DB pada setiap mutasi.
        // Session akan di-invalidate, DB cart tetap aman dan
        // akan di-restore ke session saat user login kembali.
        Auth::guard("web")->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect("/");
    }
}
