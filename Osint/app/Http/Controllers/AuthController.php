<?php

namespace App\Http\Controllers;

use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Max failed attempts before temporary lock (per IP per 15 minutes)
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:100',
            'password' => 'required|string|min:6',
        ]);

        $ip = $request->ip();
        $username = $request->username;

        // Brute-force protection: check recent failed attempts from this IP
        $recentFails = LoginAttempt::where('ip_address', $ip)
            ->where('success', 0)
            ->where('created_at', '>=', now()->subMinutes(self::LOCKOUT_MINUTES))
            ->count();

        if ($recentFails >= self::MAX_ATTEMPTS) {
            $this->logAttempt($username, $ip, $request->userAgent(), false);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login gagal. Coba lagi dalam " . self::LOCKOUT_MINUTES . " menit.",
            ])->withInput(['username' => $username]);
        }

        // Find user by username or email
        $user = User::where('username', $username)
                    ->orWhere('email', $username)
                    ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->logAttempt($username, $ip, $request->userAgent(), false);
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->withInput(['username' => $username]);
        }

        if (!$user->is_active) {
            $this->logAttempt($username, $ip, $request->userAgent(), false);
            return back()->withErrors([
                'username' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.',
            ])->withInput(['username' => $username]);
        }

        // Successful login
        Auth::login($user, $request->boolean('remember'));
        $this->logAttempt($username, $ip, $request->userAgent(), true);

        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $ip,
        ]);

        $request->session()->regenerate();

        return redirect()->intended(route('search'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function logAttempt(string $username, string $ip, ?string $ua, bool $success): void
    {
        LoginAttempt::create([
            'username'   => $username,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'success'    => $success,
        ]);
    }
}
