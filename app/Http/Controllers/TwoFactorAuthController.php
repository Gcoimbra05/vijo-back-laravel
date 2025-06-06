<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserVerification;
use App\Services\TwilioService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;

class TwoFactorAuthController extends Controller
{
    protected $twilio;

    const EXPIRES_IN = 120; // minutes

    public function __construct()
    {
        $this->twilio = new TwilioService();
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'country_code' => 'required',
        ]);

        $user = User::where('mobile', $request->mobile)->where('country_code', $request->country_code)->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'statusCode' => 404,
                    'title' => 'Not Found',
                    'detail' => [
                        'message' => 'No account exists with the provided details. Please check your details or sign up for a new account.'
                    ]
                ]
            ], 404);
        }

        $code = rand(100000, 999999);
        $verification = UserVerification::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        $fullPhoneNumber = $request->country_code . $request->mobile;
        $this->twilio->sendSms($fullPhoneNumber, "Your verification code is: $code");

        return response()->json([
            "status" => true,
            'message' => 'Verification code has been successfully sent to your mobile number.',
            'results' => [
                'otp_id' => $verification->id,
            ],
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'otp_id' => 'required|exists:user_verifications,id',
        ]);

        $verification = UserVerification::where('id', $request->otp_id)
            ->where('code', $request->otp)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$verification) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'statusCode' => 400,
                    'title' => 'Invalid Code',
                    'detail' => [
                        'message' => 'The provided code is either invalid, expired, or has already been used.'
                    ]
                ]
            ], 400);
        }

        $user = User::find($verification->user_id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'statusCode' => 404,
                    'title' => 'Not Found',
                    'detail' => [
                        'message' => 'User not found.',
                    ]
                ]
            ], 404);
        }

        $verification->update(['is_used' => true]);

        $token = $user->createToken('auth_token')->plainTextToken;

        // Generate and save the refresh_token
        $refreshToken = Str::random(60);
        $user->refresh_token = $refreshToken;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'results' => [
                'userData' => $user->toArray(),
                'access_token' => $token,
                'refresh_token' => $refreshToken,
                'expires_in' => Carbon::now()->addMinutes(self::EXPIRES_IN)->timestamp,
                'loggedIn' => true,
            ],
        ]);
    }

    public function refreshToken(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'statusCode' => 401,
                    'title' => 'Unauthorized',
                    'detail' => [
                        'message' => 'No token provided.',
                    ]
                ]
            ], 401);
        }

        $plainToken = $token;
        if (strpos($token, '|') !== false) {
            [, $plainToken] = explode('|', $token, 2);
        }
        $accessToken = PersonalAccessToken::select(['id', 'tokenable_id', 'created_at', 'tokenable_type'])
            ->where('token', hash('sha256', $plainToken))
            ->first();

        if (!$accessToken) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'statusCode' => 401,
                    'title' => 'Unauthorized',
                    'detail' => [
                        'message' => 'Invalid or expired token.',
                    ]
                ],
            ], 401);
        }

        $user = User::select(['id', 'refresh_token'])->find($accessToken->tokenable_id);
        if (!$user || !$user->refresh_token) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'statusCode' => 401,
                    'title' => 'Unauthorized',
                    'detail' => [
                        'message' => 'Refresh token not found.',
                    ]
                ]
            ], 401);
        }

        $expirationMinutes = self::EXPIRES_IN;
        $expiresAt = $accessToken->created_at->addMinutes($expirationMinutes);
        $minutesLeft = now()->diffInMinutes($expiresAt, false);

        if ($minutesLeft > 5) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'statusCode' => 400,
                    'title'      => 'Token Still Valid',
                    'detail'     => [
                        'message' => 'The current token is still valid. No new token was generated.',
                        'expires_in' => $expiresAt->timestamp,
                    ]
                ]
            ], 400);
        }

        $newToken = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Token refreshed successfully.',
            'results' => [
                'access_token' => $newToken,
                'expires_in' => now()->addMinutes($expirationMinutes)->timestamp,
            ],
        ]);
    }

    public function validateToken(Request $request)
    {
        $request->validate([
            'access_token' => 'required|string',
        ]);

        $token = $request->input('access_token');

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return response()->json([
                'status' => false,
                'errors' => [
                    'statusCode' => 401,
                    'title' => 'Unauthorized',
                    'detail' => [
                        'message' => 'Invalid or expired token.',
                    ]
                ]
            ], 401);
        }

        // Optional: Check if the token is expired (if using custom expiration)
        // if ($accessToken->expires_at && $accessToken->expires_at->isPast()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Token expired.',
        //     ], 401);
        // }

        return response()->json([
            'status' => true,
            'message' => 'Token is valid.',
            'results' => [
                'user_id' => $accessToken->tokenable_id,
            ],
        ]);
    }

    public function resend2fa(Request $request)
    {
        $otp_id = $request->input('otp_id');

        if (!$otp_id) {
            return response()->json([
                'status' => false,
                'message' => 'Session expired. Please log in again.',
            ], 401);
        }

        // Find the 2FA authentication record
        $verification = UserVerification::find($otp_id);
        if (!$verification) {
            return response()->json([
                'status' => false,
                'message' => 'Authentication request not found.',
            ], 404);
        }

        // Find the active user (status = 1)
        $user = User::where('id', $verification->user_id)
            ->where('status', 1)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not authorized.',
            ], 403);
        }

        // Generate new OTP code
        $otp = rand(100000, 999999);

        // Remove old OTPs for this user
        UserVerification::where('user_id', $user->id)->delete();

        // Create new OTP record
        $newVerification = UserVerification::create([
            'user_id'    => $user->id,
            'code'       => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used'    => false,
        ]);

        // Send the new OTP via email and/or SMS
        if ($user->email) {
            Mail::raw("Your 2FA authentication code: $otp", function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Verify your account');
            });
        }

        if ($user->mobile && $user->country_code) {
            $fullPhoneNumber = $user->country_code . $user->mobile;
            $this->twilio->sendSms($fullPhoneNumber, "Your 2FA authentication code: $otp");
        }

        // Return JSON success response
        return response()->json([
            'status' => true,
            'message' => 'A new code has been resent to your email and/or mobile.',
            'results' => [
                'otp_id' => $newVerification->id,
                'expires_at' => $newVerification->expires_at,
            ],
        ]);
    }
}
