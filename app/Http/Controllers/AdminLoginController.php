<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['is_admin'] = true;

        if (Auth::attempt($credentials)) {
            Log::info('Admin logged in', ['user_id' => Auth::id()]);
            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials or not an admin user.',
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
}