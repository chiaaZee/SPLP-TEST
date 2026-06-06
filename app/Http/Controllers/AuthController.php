<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\RecaptchaValidation;

class AuthController extends Controller
{
    use RecaptchaValidation;

    public function login()
    {
        $pageConfigs = ['myLayout' => 'blank'];
        return view('content.authentications.auth-login-cover', ['pageConfigs' => $pageConfigs]);
    }

    public function authenticate(Request $request)
    {
        // $this->validateRecaptcha($request->input('g-recaptcha-response'));

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function dashboard()
    {
        return view('content.dashboard.dashboards-analytics');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function resetAccount(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            // Log before deleting
            \App\Models\RegistrationLog::create([
                'email' => $user->email,
                'name' => $user->name,
                'action' => 'reset_for_retry',
                'user_data' => $user->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $user->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('register')->with('info', 'Akun telah dihapus. Silakan daftar kembali dengan data yang benar.');
    }
}
