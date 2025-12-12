<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

use App\Models\User;


class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login.index');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $credentials['is_admin'] = true; // Ensure only admin can log in

        if (Auth::attempt($credentials)) {
            Log::info('Admin login successful', ['email' => $credentials['email']]);
            // Check if 2FA is enabled
            if (Auth::user()->two_factor_enabled) {
                return redirect()->route('admin.validate-otp');
            }

            return redirect()->intended('/admin/dashboard');
        }

        Log::info('Failed login attempt');
        return back()->withErrors([
             'email' => 'Invalid credentials or not an admin user.',
        ]);
    }

    // OTP validation
    public function showOtpForm()
    {
        return view('admin.validate_otp.index');
    }

    public function processOtp(Request $request)
    {
        // Logic to process OTP
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/admin/login');
    }
    
    public function forgotview()
    {
        return view('admin.login.forgot');
    }


    public function forgot(Request $request)
    {

        $email = $request->email ?? session('email');

        if (!$email) {
            return back()->withErrors(['email' => 'Email is required.']);
        }

        session(['email' => $email]);

        // If email is still not provided -> error
        if (!$email) {
            return back()->withErrors([
                'email' => 'Email is required.'
            ]);
        }

        // Validate email ONLY if it came from the form
        if ($request->email) {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);

            // Save email to session for resend functionality
            session(['email' => $email]);
        }

        // Verify if user exists and is admin
        $user = User::where('email', $email)->where('is_admin', true)->first();

        if (!$user) {
            
            return back()->withErrors([
                'email' => 'This email does not belong to an admin user.',
            ]);
        }

        // Generate random 5-digit token
        $token = rand(10000, 99999);

        // Save token to database (table password_reset_tokens)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => now()]
        );

        // Log the token generation (or send a real email)
        Log::info('Password reset token generated', [
            'email' => $email,
            'token' => $token
        ]);

        // Redirect to token validation page
        return redirect()
            ->route('admin.password.validatetoken')
            ->with('success', 'Token sent to your email.');
    }

    public function showvalidatetoken()
    {
        return view('admin.login.validatetoken');
    }

    public function validatetoken(Request $request)
    {
        $request->validate([
            'code' => 'required|array|size:5',
        ]);

        // Combine the token digits
        $tokenInput = implode('', $request->code);

        // Email comes from session
        $email = session('email');

        if (!$email) {
            return redirect()->route('admin.password.forgot')
                ->withErrors(['email' => 'Session expired, please try again.']);
        }

        // Query token from database
        $record = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $tokenInput)
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Invalid token.']);
        }
        
        // Check if token is older than 5 minutes
        if (now()->diffInMinutes($record->created_at) > 5) {

            // Delete expired token
            DB::table('password_reset_tokens')->where('email', $email)->delete();

            // Remove email from session to prevent reuse
            session()->forget('email');

            // Redirect to forgot with warning
            return redirect()->route('admin.password.forgot')
                ->withErrors(['email' => 'The token has expired. Please request a new one.']);
        }


        // **Valid token -> send user to reset password page**
        session(['email' => $email]);
        return redirect()->route('admin.resetpassword.show');
    }

    public function showresetpassword()
    {
        return view('admin.login.resetpassword');
    }

    public function resetpassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:6',
                'confirmed',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>]/',
            ],
        ], [
            'password.regex' => 'The password must contain at least one uppercase letter, one number and one special character.',
            'password.min' => 'The password must have at least 6 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);
        Log::info('✅ Password validation passed.');

        // Email from session
        $email = session('email');
        if (!$email) {
            Log::warning('⛔ Session expired - empty email.');
            return redirect()->route('admin.password.forgot')
                ->withErrors(['email' => 'Session expired, please try again.']);
        }

        // Update password
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = bcrypt($request->password);
            $user->save();

            DB::table('password_reset_tokens')->where('email', $email)->delete();

            return redirect()->route('admin.login')
                ->with('success', 'Password reset successfully. You can now log in.');
        } else {
            Log::error("⛔ User not found for email: $email");
            return redirect()->route('admin.password.forgot')
                ->withErrors(['email' => 'User not found.']);
        }
    }

}
