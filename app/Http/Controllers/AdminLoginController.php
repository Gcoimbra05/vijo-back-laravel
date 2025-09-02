<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login.index');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['is_admin'] = true; // Garante que só admin loga

        if (Auth::attempt($credentials)) {
            Log::info('Admin login successful', ['email' => $credentials['email']]);
            // validate-ot
            if (Auth::user()->two_factor_enabled) {
                return redirect()->route('admin.validate-otp');
            }

            return redirect()->intended('/admin/dashboard');
        }

        Log::info('passou');
        return back()->withErrors([
             'email' => 'Invalid credentials or not an admin user.',
        ]);
    }

    // otp
    public function showOtpForm()
    {
        return view('admin.validate_otp.index');
    }

    public function processOtp(Request $request)
    {
        // Lógica para processar o OTP
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}
