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

    // fake routes
    public function getCountries () {
        return response()->json([
            'status' => true,
            'message' => '',
            'results' => [
                [
                    "code" => 1,
                    "label" => "US (+1)"
                ],
                [
                    "code" => 63,
                    "label" => "PH (+63)"
                ],
                [
                    "code" => 91,
                    "label" => "IN (+91)"
                ],
                [
                    "code" => 972,
                    "label" => "IL (+972)"
                ],
                [
                    "code" => 385,
                    "label" => "HR (+385)"
                ],
            ],
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

    public function getOnboardingContent()
    {
        $responseData = [
            'status'  => true,
            'message' => '',
            'results' => [
                "onboardingContents" => [
                    [
                        "id" => "1",
                        "pageSlug" => "home",
                        "pageName" => "Home",
                        "title" => "YOUR LIFE, YOUR STORY",
                        "subtitle" => "Let's VIJO!",
                        "description" => "Safely record your thoughts, ideas, and memories. Reflect, share, and track your emotional well-being.",
                        "message" => ""
                    ],
                    [
                        "id" => "2",
                        "pageSlug" => "sign_in_welcome",
                        "pageName" => "Sign In - Welcome Screen",
                        "title" => "Welcome Back!",
                        "subtitle" => "Welcome back to VIJO!",
                        "description" => "We’re here to support your journey of self-discovery, reflection, and growth. Let’s begin by learning a little about you.",
                        "message" => ""
                    ],
                    [
                        "id" => "3",
                        "pageSlug" => "sign_in_email_address",
                        "pageName" => "Sign In - Email Address",
                        "title" => "Step",
                        "subtitle" => "What is your email address?",
                        "description" => "We’ll send you a verification code to make sure it’s really you.",
                        "message" => ""
                    ],
                    [
                        "id" => "4",
                        "pageSlug" => "sign_in_password",
                        "pageName" => "Sign In - Password",
                        "title" => "Step",
                        "subtitle" => "Enter Your Password",
                        "description" => "",
                        "message" => ""
                    ],
                    [
                        "id" => "5",
                        "pageSlug" => "sign_in_forgot_password",
                        "pageName" => "Sign In - Forgot Password",
                        "title" => "Step",
                        "subtitle" => "Forgot Your Password?",
                        "description" => "",
                        "message" => ""
                    ],
                    [
                        "id" => "6",
                        "pageSlug" => "sign_in_verification_code",
                        "pageName" => "Sign In - Verification Code",
                        "title" => "Step",
                        "subtitle" => "Enter Your Verification Code",
                        "description" => "You should have received a 6-digit code on your phone.",
                        "message" => ""
                    ],
                    [
                        "id" => "7",
                        "pageSlug" => "sign_up_welcome",
                        "pageName" => "Sign Up - Welcome Screen",
                        "title" => "Registration",
                        "subtitle" => "Welcome to VIJO!  ",
                        "description" => "We're here to support your journey of self-discovery, reflection, and growth. Let's begin by learning a little about you.",
                        "message" => "We're here to support your journey of self-discovery, reflection, and growth. Let's begin by learning a little about you."
                    ],
                    [
                        "id" => "8",
                        "pageSlug" => "sign_up_personal_information",
                        "pageName" => "Sign Up - Personal Information",
                        "title" => "Register",
                        "subtitle" => "What's your name?",
                        "description" => "Your name is used to self-identify within VIJO.  ",
                        "message" => ""
                    ],
                    [
                        "id" => "9",
                        "pageSlug" => "sign_up_email_address",
                        "pageName" => "Sign Up - Email Address",
                        "title" => "Register",
                        "subtitle" => "What's your email address?",
                        "description" => "We use your email address to verify your account and to send VIJO updates.",
                        "message" => ""
                    ],
                    [
                        "id" => "10",
                        "pageSlug" => "sign_up_phone_number",
                        "pageName" => "Sign Up - Phone Number",
                        "title" => "Register",
                        "subtitle" => "Enter Your Mobile Number ",
                        "description" => "Get a magic link to login to the mobile app and began capturing your story.",
                        "message" => ""
                    ],
                    [
                        "id" => "11",
                        "pageSlug" => "sign_up_verification_code",
                        "pageName" => "Sign Up - Verification Code",
                        "title" => "Register",
                        "subtitle" => "Please enter the verification code",
                        "description" => "You should have received a 6-digit code to your phone.",
                        "message" => ""
                    ],
                    [
                        "id" => "12",
                        "pageSlug" => "membership_listing",
                        "pageName" => "Membership - Listing",
                        "title" => "Unlock Premium Content with VIJO +Plus.",
                        "subtitle" => "Unlock Premium Content with VIJO +Plus.",
                        "description" => "Upgrade from freemium to premium for special guides, complete VIJO history, and comprehensive emotional insights.",
                        "message" => ""
                    ],
                    [
                        "id" => "13",
                        "pageSlug" => "membership-plans",
                        "pageName" => "Membership-Plans",
                        "title" => "Unlock Premium Content with VIJO +Plus",
                        "subtitle" => "Unlock Premium Content with VIJO +Plus",
                        "description" => "Upgrade from freemium to premium for special guides, complete VIJO history, and comprehensive emotional insights.",
                        "message" => ""
                    ],
                    [
                        "id" => "14",
                        "pageSlug" => "share_vijo",
                        "pageName" => "Share Vijo",
                        "title" => "Share Your VIJO",
                        "subtitle" => "Share your story",
                        "description" => "Sharing a precious memory with loved ones strengthens your bond, fosters emotional connection, and creates a sense of belonging.",
                        "message" => ""
                    ],
                ],
                "onboardingEmoji" => "1f3a5"
            ]
        ];

        return response()->json($responseData);
    }

    // getInformationContent
    public function getInformationContent()
    {
        $responseData = [
            'status'  => true,
            'message' => '',
            'results' => [
                "informationContents" => [
                    [
                        "id" => "1",
                        "pageSlug" => "emotional_outcome",
                        "pageName" => "Emotional Outcome",
                        "title" => "Emotional Outcome",
                        "description" => "Emotional Outcome, Emotional intelligence (EQ) is the ability to understand, interpret, and control emotions to better communicate and relate to others constructively."
                    ],
                    [
                        "id" => "2",
                        "pageSlug" => "emotional_insights",
                        "pageName" => "Emotional Insights",
                        "title" => "Emotional Insights",
                        "description" => "Emotional intelligence (EQ) is the ability to understand, interpret, and control emotions to better communicate and relate to others constructively."
                    ],
                    [
                        "id" => "3",
                        "pageSlug" => "transcription",
                        "pageName" => "Transcription",
                        "title" => "Transcription",
                        "description" => "Emotional intelligence (EQ) is the ability to understand, interpret, and control emotions to better communicate and relate to others constructively."
                    ]
                ],
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
        return Catalog::where('is_promotional', true)
            ->get([
                'id',
                'title',
                'description',
                'is_premium as isPremium',
                'emoji',
                'category_id'
            ])
            ->map(function ($catalog) {
                return [
                    'id' => (string)$catalog->id,
                    'title' => $catalog->title,
                    'description' => $catalog->description,
                    'isPremium' => (string)($catalog->isPremium ?? 0),
                    'emoji' => $catalog->emoji,
                    'category_id' => $catalog->category_id,
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
                "myJournals" => VideoRequestController::getMyVideoRequests($request)->getData(),
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
                "insightFilters" => self::getInsightFilters(),
                "current_date" => now()->toDateString(),
                "responceCount" => [
                    "to_count" => 0,
                    "from_count" => 0
                ],
                "userPlan" => [
                    "user_status" => SubscriptionController::getUserPlanStatus()->getData()->results,
                ],
                "membershipPlan" => MembershipPlanController::getMembershipPlans(), // 'id, slug, title, description'
                "plans" => [], // 'id, slug, name, description, payment_link'
                "guidedToursTaken" => $user->guided_tours,
            ]
        ]);
    }

    public static function getInsightFilters()
    {
        return [
            "emotion_datasets" => [
                [
                    "id" => "1",
                    "metric_id" => "emotion##1",
                    "name" => "Stress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "24",
                    "metric_id" => "emotion##24",
                    "name" => "High JQ",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "22",
                    "metric_id" => "emotion##22",
                    "name" => "Low JQ",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "3",
                    "metric_id" => "emotion##3",
                    "name" => "Excitement",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "6",
                    "metric_id" => "emotion##6",
                    "name" => "Confidence",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "5",
                    "metric_id" => "emotion##5",
                    "name" => "Risk",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "2",
                    "metric_id" => "emotion##2",
                    "name" => "Energy",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "4",
                    "metric_id" => "emotion##4",
                    "name" => "Uncertainty",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "7",
                    "metric_id" => "emotion##7",
                    "name" => "Happiness",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "8",
                    "metric_id" => "emotion##8",
                    "name" => "Upset",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "13",
                    "metric_id" => "emotion##13",
                    "name" => "Hesitation",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "15",
                    "metric_id" => "emotion##15",
                    "name" => "Passion",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "9",
                    "metric_id" => "emotion##9",
                    "name" => "Anger",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "16",
                    "metric_id" => "emotion##16",
                    "name" => "Personality",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "10",
                    "metric_id" => "emotion##10",
                    "name" => "Emotional",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "12",
                    "metric_id" => "emotion##12",
                    "name" => "Anticipation",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "11",
                    "metric_id" => "emotion##11",
                    "name" => "Concentration",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "14",
                    "metric_id" => "emotion##14",
                    "name" => "Thinking",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "29",
                    "metric_id" => "emotion##29",
                    "name" => "OCA",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "17",
                    "metric_id" => "emotion##17",
                    "name" => "Uneasy",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "18",
                    "metric_id" => "emotion##18",
                    "name" => "Energetic Logical",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "19",
                    "metric_id" => "emotion##19",
                    "name" => "Energetic Emotional",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "20",
                    "metric_id" => "emotion##20",
                    "name" => "Stressed Emotional",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "21",
                    "metric_id" => "emotion##21",
                    "name" => "Stressed Logical",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "23",
                    "metric_id" => "emotion##23",
                    "name" => "Median JQ",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "25",
                    "metric_id" => "emotion##25",
                    "name" => "Risk 1",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "26",
                    "metric_id" => "emotion##26",
                    "name" => "Risk 2",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "35",
                    "metric_id" => "emotion##35",
                    "name" => "LVAEmoStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "66",
                    "metric_id" => "emotion##66",
                    "name" => "SPBth",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "70",
                    "metric_id" => "emotion##70",
                    "name" => "SPBtl_DIF",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "57",
                    "metric_id" => "emotion##57",
                    "name" => "SOS",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "37",
                    "metric_id" => "emotion##37",
                    "name" => "LVAENRStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "81",
                    "metric_id" => "emotion##81",
                    "name" => "nCHL",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "34",
                    "metric_id" => "emotion##34",
                    "name" => "LVAGLBStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "65",
                    "metric_id" => "emotion##65",
                    "name" => "SPBT",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "33",
                    "metric_id" => "emotion##33",
                    "name" => "LVARiskStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "53",
                    "metric_id" => "emotion##53",
                    "name" => "MaxVolAmp",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "58",
                    "metric_id" => "emotion##58",
                    "name" => "SPJ",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "38",
                    "metric_id" => "emotion##38",
                    "name" => "LVAMentalEffort",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "67",
                    "metric_id" => "emotion##67",
                    "name" => "SPBtl",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "54",
                    "metric_id" => "emotion##54",
                    "name" => "P1",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "55",
                    "metric_id" => "emotion##55",
                    "name" => "P2",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "56",
                    "metric_id" => "emotion##56",
                    "name" => "P3",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "39",
                    "metric_id" => "emotion##39",
                    "name" => "LVASOSStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "79",
                    "metric_id" => "emotion##79",
                    "name" => "SPJcomp",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "27",
                    "metric_id" => "emotion##27",
                    "name" => "EmoBalance",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "71",
                    "metric_id" => "emotion##71",
                    "name" => "SPBth_DIF",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "28",
                    "metric_id" => "emotion##28",
                    "name" => "Imagin",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "30",
                    "metric_id" => "emotion##30",
                    "name" => "ExtremeEmotion",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "31",
                    "metric_id" => "emotion##31",
                    "name" => "CogHighLowBalance",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "32",
                    "metric_id" => "emotion##32",
                    "name" => "Dissat",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "36",
                    "metric_id" => "emotion##36",
                    "name" => "LVACOGStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "40",
                    "metric_id" => "emotion##40",
                    "name" => "AVJ",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "41",
                    "metric_id" => "emotion##41",
                    "name" => "CHL",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "42",
                    "metric_id" => "emotion##42",
                    "name" => "Fant",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "43",
                    "metric_id" => "emotion##43",
                    "name" => "Fcen",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "44",
                    "metric_id" => "emotion##44",
                    "name" => "Fflic",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "45",
                    "metric_id" => "emotion##45",
                    "name" => "Fmain",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "46",
                    "metric_id" => "emotion##46",
                    "name" => "FmainPos",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "47",
                    "metric_id" => "emotion##47",
                    "name" => "Fq",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "48",
                    "metric_id" => "emotion##48",
                    "name" => "FsubCog",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "49",
                    "metric_id" => "emotion##49",
                    "name" => "FsubEmo",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "50",
                    "metric_id" => "emotion##50",
                    "name" => "Fx",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "51",
                    "metric_id" => "emotion##51",
                    "name" => "JQ",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "52",
                    "metric_id" => "emotion##52",
                    "name" => "LJ",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "59",
                    "metric_id" => "emotion##59",
                    "name" => "SPJhl",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "60",
                    "metric_id" => "emotion##60",
                    "name" => "SPJll",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "61",
                    "metric_id" => "emotion##61",
                    "name" => "SPJsh",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "62",
                    "metric_id" => "emotion##62",
                    "name" => "SPJsl",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "63",
                    "metric_id" => "emotion##63",
                    "name" => "SPT",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "64",
                    "metric_id" => "emotion##64",
                    "name" => "SPST",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "68",
                    "metric_id" => "emotion##68",
                    "name" => "SPSth",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "69",
                    "metric_id" => "emotion##69",
                    "name" => "SPStl",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "72",
                    "metric_id" => "emotion##72",
                    "name" => "SPJsav",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "73",
                    "metric_id" => "emotion##73",
                    "name" => "SPJlav",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "74",
                    "metric_id" => "emotion##74",
                    "name" => "VOL1",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "75",
                    "metric_id" => "emotion##75",
                    "name" => "VOL2",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "76",
                    "metric_id" => "emotion##76",
                    "name" => "intCHL",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "77",
                    "metric_id" => "emotion##77",
                    "name" => "SPTJtot",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "78",
                    "metric_id" => "emotion##78",
                    "name" => "SPJdist",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "80",
                    "metric_id" => "emotion##80",
                    "name" => "JHLratio",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "82",
                    "metric_id" => "emotion##82",
                    "name" => "CHLdif",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "83",
                    "metric_id" => "emotion##83",
                    "name" => "CCCHL",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "84",
                    "metric_id" => "emotion##84",
                    "name" => "sptBdiff",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "85",
                    "metric_id" => "emotion##85",
                    "name" => "HASv",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "86",
                    "metric_id" => "emotion##86",
                    "name" => "JQcl",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "87",
                    "metric_id" => "emotion##87",
                    "name" => "AVJcl",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "88",
                    "metric_id" => "emotion##88",
                    "name" => "AF1",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "89",
                    "metric_id" => "emotion##89",
                    "name" => "AF2",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "90",
                    "metric_id" => "emotion##90",
                    "name" => "AF3",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "91",
                    "metric_id" => "emotion##91",
                    "name" => "AF4",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "92",
                    "metric_id" => "emotion##92",
                    "name" => "AF5",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "93",
                    "metric_id" => "emotion##93",
                    "name" => "AF6",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "94",
                    "metric_id" => "emotion##94",
                    "name" => "AF7",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "95",
                    "metric_id" => "emotion##95",
                    "name" => "AF8",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "96",
                    "metric_id" => "emotion##96",
                    "name" => "AF9",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "97",
                    "metric_id" => "emotion##97",
                    "name" => "AF10",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "98",
                    "metric_id" => "emotion##98",
                    "name" => "emoEnergyBalance",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "99",
                    "metric_id" => "emotion##99",
                    "name" => "mentalEffort",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "100",
                    "metric_id" => "emotion##100",
                    "name" => "atmosphere",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "101",
                    "metric_id" => "emotion##101",
                    "name" => "emoPlayerEnergy",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "102",
                    "metric_id" => "emotion##102",
                    "name" => "emoPlayerJoy",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "103",
                    "metric_id" => "emotion##103",
                    "name" => "emoPlayerSad",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "104",
                    "metric_id" => "emotion##104",
                    "name" => "emoPlayerAggression",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "105",
                    "metric_id" => "emotion##105",
                    "name" => "emoPlayerStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "106",
                    "metric_id" => "emotion##106",
                    "name" => "emoPlayerRisk",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "107",
                    "metric_id" => "emotion##107",
                    "name" => "finalRiskLevel",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "108",
                    "metric_id" => "emotion##108",
                    "name" => "EDP-Energetic",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "109",
                    "metric_id" => "emotion##109",
                    "name" => "EDP-Passionate",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "110",
                    "metric_id" => "emotion##110",
                    "name" => "EDP-Emotional",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "111",
                    "metric_id" => "emotion##111",
                    "name" => "EDP-Uneasy",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "112",
                    "metric_id" => "emotion##112",
                    "name" => "EDP-Stressful",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "113",
                    "metric_id" => "emotion##113",
                    "name" => "EDP-Thoughtful",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "114",
                    "metric_id" => "emotion##114",
                    "name" => "EDP-Confident",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "115",
                    "metric_id" => "emotion##115",
                    "name" => "EDP-Concentrated",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "116",
                    "metric_id" => "emotion##116",
                    "name" => "EDP-Anticipation",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "117",
                    "metric_id" => "emotion##117",
                    "name" => "EDP-Hesitation",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "118",
                    "metric_id" => "emotion##118",
                    "name" => "callPriority",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "119",
                    "metric_id" => "emotion##119",
                    "name" => "callPriorityAgent",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "120",
                    "metric_id" => "emotion##120",
                    "name" => "sampleSize",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "121",
                    "metric_id" => "emotion##121",
                    "name" => "cPor",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "122",
                    "metric_id" => "emotion##122",
                    "name" => "offlineLVAValue",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "123",
                    "metric_id" => "emotion##123",
                    "name" => "offlineLVARiskStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "124",
                    "metric_id" => "emotion##124",
                    "name" => "offlineLVARiskProbability",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "125",
                    "metric_id" => "emotion##125",
                    "name" => "offlineLVARiskEmotionStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "126",
                    "metric_id" => "emotion##126",
                    "name" => "offlineLVARiskCognitiveStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "127",
                    "metric_id" => "emotion##127",
                    "name" => "offlineLVARiskGlobalStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "128",
                    "metric_id" => "emotion##128",
                    "name" => "offlineLVARiskFrgStress",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "129",
                    "metric_id" => "emotion##129",
                    "name" => "offlineLVARiskSubjectiveEffortLevel",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "130",
                    "metric_id" => "emotion##130",
                    "name" => "offlineLVARiskDeceptionPatterns",
                    "emotionEmoji" => null
                ],
                [
                    "id" => "131",
                    "metric_id" => "emotion##131",
                    "name" => "Self Honesty",
                    "emotionEmoji" => null
                ]
            ],
            "outcome_datasets" => [
                [
                    "id" => 1,
                    "metric_id" => "performance##012",
                    "name" => "EP"
                ],
                [
                    "id" => 2,
                    "metric_id" => "performance##0",
                    "name" => "KPI 1"
                ],
                [
                    "id" => 3,
                    "metric_id" => "performance##0_0",
                    "name" => "KPI 1 - Metric 1"
                ],
                [
                    "id" => 4,
                    "metric_id" => "performance##0_1",
                    "name" => "KPI 1 - Metric 2"
                ],
                [
                    "id" => 5,
                    "metric_id" => "performance##0_2",
                    "name" => "KPI 1 - Metric 3"
                ]
            ]
        ];
    }
}
