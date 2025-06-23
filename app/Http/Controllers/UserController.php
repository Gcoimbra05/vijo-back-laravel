<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TwoFactorAuthController;
use App\Models\Catalog;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function show($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            // 'last_name' => 'required|string|max:100',
            'email' => 'required|string|email',
            // 'password' => 'required|string',
            'country_code' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:20',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'A user with this email already exists.',
            ], 409);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'country_code' => $request->country_code,
            'mobile' => $request->mobile
        ]);

        // Send OTP
        $twoFactorAuth = new TwoFactorAuthController();
        $otp_result = $twoFactorAuth->sendCode(new Request([
            'mobile' => $request->mobile,
            'country_code' => $request->country_code,
        ]));

        if ($otp_result->getStatusCode() !== 200) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP.',
            ], 500);
        }

        $otp_data = json_decode($otp_result->getContent(), true);
        $otp_id = $otp_data['results']['otp_id'] ?? null;

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully.',
            'results' => [
                'userData' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'country_code' => $user->country_code,
                    'mobile' => $user->mobile,
                ],
                'otp_id' => $otp_id,
                'expires_in' => Carbon::now()->addMinutes(config('sanctum.expiration', 60))->timestamp,
                'loggedIn' => true,
            ],
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'first_name' => 'sometimes|required|string|max:100',
            'last_name' => 'sometimes|required|string|max:100',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8',
            'country_code' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:20',
            'reminders' => 'sometimes|boolean',
            'notifications' => 'sometimes|boolean',
            'timezone' => 'sometimes|string|max:50',
            'optInNewsUpdates' => 'sometimes|boolean',
        ]);

        $user->update($request->only('first_name', 'last_name', 'email', 'country_code', 'mobile', 'reminders', 'notifications', 'timezone', 'optInNewsUpdates'));

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }
        return response()->json(['message' => 'User not found'], 404);
    }

    public function updateGuidedTour(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $request->validate([
            'guided_tours' => 'required|in:0,1',
        ]);

        $user->guided_tours = $request->guided_tours;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Guided tour updated successfully'
        ]);
    }

    public function getSubscriptionPlans(Request $request)
    {
        $plans = MembershipPlan::where('status', 1)
            ->orderBy('id', 'ASC')
            ->get(['id', 'slug', 'name as title', 'description', 'payment_link']);

        $user = $request->user();
        $userPlan = null;

        if ($user && $user->membership_plan_id) {
            $userPlanModel = MembershipPlan::find($user->plan_id);
            if ($userPlanModel) {
                $userPlan = [
                    'id'          => (string)$userPlanModel->id,
                    'slug'        => $userPlanModel->slug,
                    'title'       => $userPlanModel->name,
                    'description' => $userPlanModel->description,
                ];
            }
        }

        $responseData = [
            'status'  => true,
            'message' => '',
            'results' => [
                'plans' => $plans->map(function ($plan) {
                    return [
                        'id'          => (string)$plan->id,
                        'slug'        => $plan->slug,
                        'title'       => $plan->title,
                        'description' => $plan->description,
                        'paymentLink' => $plan->payment_link,
                    ];
                })->toArray(),
                'userPlan' => $userPlan,
            ]
        ];

        return response()->json($responseData);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => true,
                'message' => 'Password reset link sent to your email.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Unable to send reset link. Please check the email address.'
        ], 400);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'status' => true,
                'message' => 'Password has been reset successfully.'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid token or email.'
        ], 400);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'hash' => 'required|string',
        ]);

        $user = User::find($request->id);

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }

        if (!hash_equals(sha1($user->email), $request->hash)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid verification link.'
            ], 400);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully.'
        ]);
    }

    public function resendEmailVerification(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found.'
            ], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => false,
                'message' => 'Email already verified.'
            ], 400);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Verification email resent.'
        ]);
    }

    public static function getGuidedTours()
    {
        return [
            [
                "id" => "1",
                "title" => "Welcome to vijo",
                "description" => "Let's quickly show you around so you get to journaling.",
                "target" => ""
            ],
            [
                "id" => "2",
                "title" => "Home",
                "description" => "Think of this as your vijo hub for journals and recommendations.",
                "target" => "home"
            ],
            [
                "id" => "3",
                "title" => "Gallery",
                "description" => "Here is where you'll find vijo's and memories you've recorded.",
                "target" => "gallery"
            ],
            [
                "id" => "4",
                "title" => "Insights",
                "description" => "We're a community in support of each other along the journey.",
                "target" => "insights"
            ],
            [
                "id" => "5",
                "title" => "Let's vijo",
                "description" => "Tap the icon to begin a new vijo and share your thoughts.",
                "target" => "add_new"
            ]
        ];
    }

    public static function getPromotionalCatalogs()
    {
        return Catalog::with('category')
            ->where('is_promotional', true)
            ->get([
                'id',
                'title',
                'description',
                'is_premium',
                'emoji',
                'category_id',
                'video_type_id'
            ])
            ->map(function ($catalog) {
                return [
                    'id' => (string)$catalog->id,
                    'title' => $catalog->title,
                    'description' => $catalog->description,
                    'is_premium' => (string)($catalog->is_premium ?? 0),
                    'emoji' => $catalog->emoji,
                    'category_id' => $catalog->category_id,
                    'video_type_id' => $catalog->video_type_id,
                    'category_name' => $catalog->category ? $catalog->category->name : null,
                ];
            })
            ->toArray();
    }

    public function getDashboardData(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            "status" => true,
            "message" => "",
            "results" => [
                "guidedTours" => self::getGuidedTours(),
                "categories" => CategoryController::getCategories(),
                "promotionalCatalogs" => self::getPromotionalCatalogs(),
                "timezoneMenus" => SettingsController::getTimezones(),
                "myJournals" => VideoRequestController::getMyVideoRequests(),
                "graphTypes" => [
                    "bar" => "Bar",
                    "area" => "Area",
                    "line" => "Line"
                ],
                "filterByLabels" => [
                    "daily" => "Current Week",
                    "last5Weeks" => "Last 5 Weeks",
                    "weekly" => "Current Month",
                    "last3Months" => "Last 3 Months",
                    "last6Months" => "Last 6 Months",
                    "last12Months" => "Last 12 Months",
                    "sinceStart" => "Since Start",
                    "custom" => "Custom"
                ],
                "viewByLabels" => [
                    "days" => "Daily",
                    "days_of_week" => "Day of Week",
                    "weeks" => "Weekly",
                    "months" => "Monthly",
                    "quarters" => "Quarterly",
                    "years" => "Yearly"
                ],
                "rangeTypeLabels" => [
                    "lva" => "Normalized",
                    "raw" => "Raw"
                ],
                "userTags" => TagController::getUserTags(),
                "insightFilters" => SettingsController::getInsightFilters(),
                "current_date" => now()->toDateString(),
                "responceCount" => [
                    "to_count" => 0,
                    "from_count" => 0
                ],
                "userPlan" => [
                    "user_status" => 'active', #SubscriptionController::getUserPlanStatus(),
                ],
                "membershipPlan" => MembershipPlanController::getMembershipPlans(), // 'id, slug, title, description'
                "plans" => [], // 'id, slug, name, description, payment_link'
                "guidedToursTaken" => $user->guided_tours,
            ]
        ]);
    }
}
