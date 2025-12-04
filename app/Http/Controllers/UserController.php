<?php

namespace App\Http\Controllers;

use App\Helpers\GeneralHelper;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\TwoFactorAuthController;
use App\Models\Catalog;
use App\Models\EmloResponse;
use App\Models\MembershipPlan;
use App\Models\UserLogin;
use App\Models\UserVerification;
use App\Models\Video;
use App\Services\Emlo\EmloInsights\EmloInsightsService;
use App\Services\TwilioService;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Intl\Countries;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Affiliate;
use App\Models\VideoRequest;
use App\Models\VijoPlan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use OwenIt\Auditing\Models\Audit;

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

    /**
     * Permanently delete all user data from the system (account closure).
     * This method deletes the user and all related records to avoid foreign key issues.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAccount(Request $request)
    {
        Log::info('deleteAccount');
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['status' => false, 'message' => 'User not authenticated.'], 401);
        }

        \DB::beginTransaction();
        try {
            $videoRequests = \App\Models\VideoRequest::where('user_id', $userId)->get();
            foreach ($videoRequests as $vr) {
                $videos = Video::where('request_id', $vr->id)->get();
                foreach ($videos as $video) {
                    $video->delete();
                }
                \App\Models\Transcript::where('request_id', $vr->id)->delete();
                \App\Models\LlmResponse::where('request_id', $vr->id)->delete();
                $emloResponses = EmloResponse::where('request_id', $vr->id)->get();
                foreach ($emloResponses as $emloResponse) {
                    \App\Models\EmloResponseValue::where('response_id', $emloResponse->id)->delete();
                    $emloResponse->delete();
                }
                \App\Models\LlmResponse::where('request_id', $vr->id)->delete();
                \App\Models\KpiMetricValue::where('request_id', $vr->id)->delete();
                \App\Models\EmloInsightsParamAggregate::where('request_id', $vr->id)->delete();
                \App\Models\CredScoreValue::where('request_id', $vr->id)->delete();
                \App\Models\CredScoreInsightsAggregate::where('request_id', $vr->id)->delete();
                $vr->delete();
            }
            \App\Models\Tag::where('created_by_user', $userId)->delete();
            \App\Models\CatalogAnswer::where('user_id', $userId)->delete();
            \App\Models\Subscription::where('user_id', $userId)->delete();
            \App\Models\Affiliate::where('user_id', $userId)->delete();
            \App\Models\TrustedDevice::where('user_id', $userId)->delete();
            \App\Models\UserVerification::where('user_id', $userId)->delete();
            \App\Models\UserLogin::where('user_id', $userId)->delete();
            \App\Models\Contact::where('user_id', $userId)->delete();
            \App\Models\ContactGroup::where('user_id', $userId)->delete();
            \App\Models\LlmTemplate::where('user_id', $userId)->delete();
            \App\Models\VideoRequest::where('ref_user_id', $userId)->update(['ref_user_id' => null]);
            \App\Models\UserLogin::where('user_id', $userId)->delete();
            \App\Models\QuickGoal::where('user_id', $userId)->delete();
            \App\Models\UserFeedback::where('user_id', $userId)->delete();
            $user = User::where('id', $userId)->first();
            $user->delete();

            \DB::commit();

            Log::info('User and related data deleted successfully.');

            if ($user && method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            return response()->json(['status' => true, 'message' => 'Account and all user data deleted successfully.']);
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Error deleting account: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'email' => 'required|string|email',
            'password' => 'required|string',
            'confirm_password' => 'nullable|string|same:password',
            'country_code' => 'nullable|string|max:10',
            'mobile' => 'nullable|string|max:20',
            'optInNewsUpdates' => 'sometimes|boolean',
            'timezone' => 'nullable|string|max:100',
        ]);

        if (User::where('email', $request->email)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'A user with this email already exists.',
            ], 409);
        }

        if (!empty($request->mobile) && !empty($request->country_code)) {
            $existsPhone = User::where('mobile', $request->mobile)
                ->where('country_code', $request->country_code)
                ->exists();
            if ($existsPhone) {
                return response()->json([
                    'status' => false,
                    'message' => 'A user with this phone number already exists.',
                ], 409);
            }
        }

        if ($request->password !== $request->confirm_password) {
            return response()->json([
                'status' => false,
                'message' => 'Password and confirm password do not match.',
            ], 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'country_code' => GeneralHelper::onlyNumbers($request->country_code),
            'mobile' => GeneralHelper::onlyNumbers($request->mobile),
            'optInNewsUpdates' => $request->optInNewsUpdates ?? 0,
            'timezone' => $request->timezone,
        ]);

        // Send OTP
        $twoFactorAuth = new TwoFactorAuthController();
        $otp_result = $twoFactorAuth->sendCode(new Request([
            'type' => 'email',
            'email' => $request->email,
            'mobile' => GeneralHelper::onlyNumbers($request->mobile),
            'country_code' => GeneralHelper::onlyNumbers($request->country_code),
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
            'guided_tours' => 'sometimes|boolean',
            'description' => 'nullable|string|max:255',
            'notifications' => 'sometimes|boolean',
            'reminders' => 'sometimes|boolean',
            'timezone' => 'sometimes|string|max:100',
            'optInNewsUpdates' => 'sometimes|boolean',
            'two_factor_enabled' => 'sometimes|boolean',
        ]);

        $user->update([
            'country_code' => GeneralHelper::onlyNumbers($request->country_code),
            'mobile' => GeneralHelper::onlyNumbers($request->mobile),
            'notifications' => $request->notifications ?? 0,
            'reminders' => $request->reminders ?? 0,
            'optInNewsUpdates' => $request->optInNewsUpdates ?? 0,
            'two_factor_enabled' => $request->two_factor_enabled ?? 0,
        ] + $request->only('first_name', 'last_name', 'email', 'guided_tours', 'description', 'timezone'));

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return request()->wantsJson()
            ? response()->json(['message' => 'User updated successfully.'])
            : redirect('admin/users')->with('success', 'User updated successfully.');
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
        $userId = Auth::id();
        $user = User::find($userId);
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
                "title" => "Welcome to Vijo",
                "description" => "Let's quickly show you around so you get to journaling.",
                "target" => ""
            ],
            [
                "id" => "2",
                "title" => "Home",
                "description" => "Think of this as your Vijo hub for journals and recommendations.",
                "target" => "home"
            ],
            [
                "id" => "3",
                "title" => "Gallery",
                "description" => "Here is where you'll find Vijo's and memories you've recorded.",
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
                "title" => "Let's Vijo",
                "description" => "Tap the icon to begin a new Vijo and share your thoughts.",
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

    public static function getGreeting($user, $timezone = 'America/New_York')
    {
        $timezone = $user->timezone ?? config('app.timezone', 'America/New_York');
        $hour = now()->setTimezone($timezone)->hour;
        if ($hour < 12) {
            $greetingTime = 'Good morning';
        } else if ($hour < 18) {
            $greetingTime = 'Good afternoon';
        } else {
            $greetingTime = 'Good evening';
        }

        return "{$greetingTime}, {$user->first_name}!";
    }

    public function getActivityData(EmloInsightsService $emlo)
    {
        $user = Auth::user();
        $userId = $user->id;

        $activity = $emlo->getCondensedUserActivity($userId);
        return response()->json([
            'status' => 'success',
            'message' => 'Activity data retrieved successfully.',
            'data' => $activity
        ]);
    }

    public function getDashboardData(Request $request, EmloInsightsService $emlo)
    {
        $user = Auth::user();
        $userId = $user->id;

        $cacheKey = "dashboard_data_user_{$userId}";
        $cacheTtl = 60;

        $activity = $emlo->getUserActivity($userId, 'last_7_days');

        $timezone = $user->timezone ?? config('app.timezone', 'America/New_York');
        $vijoOfDay = $this->handleVijoOfDay($timezone);

        $dashboardData = Cache::remember($cacheKey, $cacheTtl, function () use ($user, $activity, $timezone, $vijoOfDay) {
            return [
                "status" => true,
                "message" => "",
                "results" => [
                    "activity" => $activity,
                    "categories" => CategoryController::getCategories(),
                    "coming_soon" => [
                        'headline' => 'COMING SOON',
                        'emoji' => '🌍',
                        'title' => 'Create your World',
                        'description' => 'Build your personalized experience and shape your journey. Coming soon...',
                    ],
                    "current_date" => now()->setTimezone($timezone)->toDateString(),
                    "currentDate" => now()->setTimezone($timezone)->format('l, F j, Y'),
                    "daily_message" => [
                        'headline' => 'DAILY INSPIRATION',
                        'emoji' => '💌',
                        'title' => 'Message from Vijo',
                        'description' => 'Receive personalized insights and encouragement tailored to your journey',
                    ],
                    "filterByLabels" => [
                        "current_week" => "Current Week",
                        "last_5_weeks" => "Last 5 Weeks",
                        "current_month" => "Current Month",
                        "last_3_months" => "Last 3 Months",
                        "last_6_months" => "Last 6 Months",
                        "last_12_months" => "Last 12 Months",
                        "since_start" => "Since Start"
                    ],
                    "graphTypes" => [
                        "bar" => "Bar",
                        "area" => "Area",
                        "line" => "Line"
                    ],
                    "greeting" => self::getGreeting($user, $timezone),
                    "guidedTours" => self::getGuidedTours(),
                    "guidedToursTaken" => $user->guided_tours,
                    "insightFilters" => SettingsController::getInsightFilters(),
                    "membershipPlan" => MembershipPlanController::getMembershipPlans(),
                    "myJournals" => VideoRequestController::getMyVideoRequests(),
                    "plans" => [],
                    "promotionalCatalogs" => self::getPromotionalCatalogs(),
                    "quick_goals" => QuickGoalController::getQuickGoalInfo($user->id),
                    "rangeTypeLabels" => [
                        "lva" => "Normalized",
                        "raw" => "Raw"
                    ],
                    "responceCount" => [
                        "to_count" => 0,
                        "from_count" => 0
                    ],
                    "timezoneMenus" => SettingsController::getTimezones(),
                    "userPlan" => [
                        "user_status" => 'active', //SubscriptionController::getUserPlanStatus(),
                    ],
                    "userTags" => TagController::getUserTags(),
                    "vijo_of_day" => $vijoOfDay,
                    "viewByLabels" => [
                        "daily" => "Daily",
                        "day_of_week" => "Day of Week",
                        "weekly" => "Weekly",
                        "monthly" => "Monthly",
                        "quarterly" => "Quarterly",
                        "yearly" => "Yearly"
                    ]
                ]
            ];
        });

        return response()->json($dashboardData);
    }

    public function adminIndex()
    {
        $nav_bar = 'users';
        $users = User::all();

        $breadcrumbs = [
            ['label' => 'Users', 'url' => null],
        ];

        return view('admin.users.index', compact('users', 'nav_bar', 'breadcrumbs'));
    }

    public function deactivate($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = false;
        $user->save();

        $display_msg = array(
            'msg'   => 'Status has been changed successfully',
            'type'  => 'success',
            'icon'  => 'bx bx-check'
        );

        session()->flash('display_msg', $display_msg);

        return redirect()->to('admin/users');
    }

    public function activate($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = true;
        $user->save();

        $display_msg = array(
            'msg'   => 'Status has been changed successfully',
            'type'  => 'success',
            'icon'  => 'bx bx-check'
        );

        session()->flash('display_msg', $display_msg);

        return redirect()->to('admin/users');
    }

    public function auditLogsView($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $auditLogs = Audit::where(function($query) use ($id) {
            $query->where('user_id', $id)
                  ->orWhere(function($q) use ($id) {
                      $q->where('auditable_type', 'App\\Models\\User')
                        ->where('auditable_id', $id);
                  });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(50);

        $nav_bar = 'users';

        $breadcrumbs = [
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'Audit Logs', 'url' => null]
        ];

        return view('admin.users.audit_logs', compact('user', 'auditLogs', 'nav_bar', 'breadcrumbs'));
    }

    public function journalHistoryView($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $journalHistory = VideoRequest::where('user_id', $id)
            ->with(['catalog', 'videos'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $stats = [
            'total_journals' => VideoRequest::where('user_id', $id)->count(),
            'completed_journals' => VideoRequest::where('user_id', $id)->where('status', 'completed')->count(),
            'pending_journals' => VideoRequest::where('user_id', $id)->where('status', 'pending')->count(),
            'total_videos' => 0, // Video::where('user_id', $id)->count(),
        ];

        $nav_bar = 'users';

        $breadcrumbs = [
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'Journal History', 'url' => null]
        ];

        return view('admin.users.journal_history', compact('user', 'journalHistory', 'stats', 'nav_bar', 'breadcrumbs'));
    }

    public function edit(User $user)
    {
        $pageTitle = "Edit Person";
        $nav_bar = "Users";
        $breadcrumbs = [
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'Edit Person', 'url' => null],
        ];
        $countries = Countries::getNames();
        $membershipPlans = MembershipPlan::all();
        $contacts = Contact::getByUser($user->id);
        $contactgroups = ContactGroup::getByUser($user->id);
        $affiliates = Affiliate::getByUser($user->id);
        $vijoplans = VijoPlan::getByUser($user->id);

        $timezonesByCountry = [];
        foreach (array_keys($countries) as $code) {
            $timezonesByCountry[$code] = \DateTimeZone::listIdentifiers(\DateTimeZone::PER_COUNTRY, $code);
        }

        return view('admin.users.edit', compact('user', 'pageTitle', 'nav_bar', 'breadcrumbs', 'countries', 'timezonesByCountry', 'membershipPlans', 'contacts', 'contactgroups', 'affiliates', 'vijoplans'));
    }

    public function contactsByUser($userId)
    {
        $contacts = Contact::getByUser($userId);
        $contactgroups = ContactGroup::getByUser($userId);
        $affiliates = Affiliate::getByUser($userId);
        $vijoplans = VijoPlan::getByUser($user->id);

        $user = User::findOrFail($userId);

        return view('admin.users.contacts', compact('contacts', 'user', 'contactgroups', 'affiliates', 'vijoplans'));
    }

    public function handleVijoOfDay($timezone = 'America/New_York')
    {
        $catalogs = Catalog::where('status', 1)
            ->where('is_deleted', 0)
            ->orderBy('id', 'ASC')
            ->get(['id', 'video_type_id', 'emoji', 'title', 'description']);

        $dayOfMonth = now()->setTimezone($timezone)->day;
        $catalogIndex = $dayOfMonth - 1;
        if ($catalogIndex >= $catalogs->count()) {
            $catalogIndex = $catalogs->count() - 1;
        }
        $vijoCatalog = $catalogs[$catalogIndex] ?? null;

        $vijoOfDay = $vijoCatalog ? [
            'id' => $vijoCatalog->id,
            'vijo_type_id' => $vijoCatalog->video_type_id,
            'emoji' => $vijoCatalog->emoji,
            'title' => $vijoCatalog->title,
            'description' => $vijoCatalog->description,
        ] : [];

        return $vijoOfDay;
    }

    /**
     * Envia o link de redefinição de senha para o email informado
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $frontendUrl = config('app.url', 'https://test.vijo.me');
        $envUrl = str_replace('.com', '.me', $frontendUrl);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found.'], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        \DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => $token,
                'created_at' => now()
            ]
        );
        $redirectLink = $envUrl . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);

        Mail::send('emails.template_new', [
            'title' => "Password Reset - Vijo",
            'contentView' => 'emails.reset_password',
            'contentData' => [
                'user' => $user,
                'redirect_link' => $redirectLink
            ]
        ], function ($message) use ($user) {
            $message->to($user->email)
                ->subject("Password Reset - Vijo");
        });

        return response()->json([
            'status' => true,
            'message' => 'Reset link sent to your email.',
        ]);
    }

    /**
     * Redefine a senha e faz login
     */
    public function resetPasswordAndLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found.'], 404);
        }

        $reset = DB::table('password_resets')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();
        if (!$reset) {
            return response()->json(['status' => false, 'message' => 'Invalid token or email.'], 400);
        }

        $user->password = bcrypt($request->password);
        $refreshToken = Str::random(60);
        $user->refresh_token = $refreshToken;
        $user->is_verified = true;
        $user->last_login_date = Carbon::now();
        $user->save();

        Auth::login($user);

        DB::table('password_resets')->where('email', $request->email)->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        UserLogin::create([
            'user_id'    => $user->id,
            'logged_in_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Login successful.',
            'results' => [
                'userData' => $user->toArray(),
                'access_token' => $token,
                'refresh_token' => $refreshToken,
                'expires_in' => Carbon::now()->addMinutes(60)->timestamp,
                'loggedIn' => true,
            ],
        ]);
    }

    /**
     * Valida os dados do perfil do usuário
     */
    public function validateProfile(Request $request)
    {
        $request->validate([
            'type' => 'required|in:email,phone',
            'password' => 'required|string',
            'new_email' => 'required_if:type,email|email',
            'country_code' => 'required_if:type,phone|string',
            'mobile' => 'required_if:type,phone|string',
        ]);
        $user = Auth::user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not authenticated.'], 401);
        }
        if (!password_verify($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Incorrect password.'], 400);
        }

        $code = rand(100000, 999999);
        $verification = UserVerification::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Send email notification
        $userEmail = $request->new_email;
        if ($request->type === 'email' && !empty($userEmail)) {
            $appUrl = config('app.url');
            $envUrl = str_replace('.com', '.me', $appUrl);
            try {
                Mail::send('emails.template_new', [
                    'title' => 'Your Vijo account verification code',
                    'contentView' => 'emails.verification_code',
                    'contentData' => [
                        'recipientName'     => $user->first_name,
                        'verificationCode'  => $code,
                        'signUpUrl'         => $envUrl,
                    ]
                ], function ($message) use ($userEmail, $code) {
                    $message->to($userEmail)
                        ->subject('🔑 Your verification code is ' . $code);
                });
            } catch (\Exception $e) {
                Log::error('Error sending email: ' . $e->getMessage());
            }
        } else if (!empty($request->country_code) && !empty($request->mobile)) {
            // Send SMS
            $twoFactorController = new TwoFactorAuthController();
            $twoFactorController->sendSms($request->country_code, $request->mobile, "Vijo verification code: {$code}\n\nReply STOP to opt-out of SMS messages and receive codes via email only. Reply HELP for support. Msg & data rates may apply.");
        }

        return response()->json([
            "status" => true,
            'message' => 'Verification code has been successfully sent to your mobile number.',
            'results' => [
                'otp_id' => $verification->id,
            ],
        ]);
    }

    /**
     * Atualiza os dados do perfil do usuário logado
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'type' => 'required|in:email,phone',
            'password' => 'required|string',
            'confirmation_code' => 'required',
            'otp_id' => 'required|integer',
            'new_email' => 'required_if:type,email|email',
            'country_code' => 'required_if:type,phone|string',
            'mobile' => 'required_if:type,phone|string',
        ]);
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not authenticated.'], 401);
        }
        if (!password_verify($request->password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Incorrect password.'], 400);
        }

        $verification = UserVerification::where('id', $request->otp_id)
            ->where('code', $request->confirmation_code)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();
        if (!$verification) {
            return response()->json([
                'status' => false,
                'message' => 'The provided code is invalid, expired, or has already been used.'
            ], 400);
        }

        $verification->update(['is_used' => true]);
        if ($request->type === 'email') {
            $user->email = $request->new_email;
        } else {
            $user->country_code = GeneralHelper::onlyNumbers($request->country_code);
            $user->mobile = GeneralHelper::onlyNumbers($request->mobile);
        }
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully.',
            'results' => $user
        ]);
    }

    /**
     * Salva nova senha para o usuário logado
     */
    public function saveNewPassword(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not authenticated.'], 401);
        }
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
        if (!password_verify($request->current_password, $user->password)) {
            return response()->json(['status' => false, 'message' => 'Current password is incorrect.'], 400);
        }
        $user->password = bcrypt($request->new_password);
        $user->save();

        return response()->json(['status' => true, 'message' => 'Password changed successfully.']);
    }

    /**
     * Atualiza o status do 2FA do usuário logado
     */
    public function updateTwoFactor(Request $request)
    {
        $user = User::find(Auth::id());
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 401);
        }
        $request->validate([
            'enabled' => 'required|boolean',
        ]);
        $user->two_factor_enabled = $request->enabled;
        $user->save();

        return response()->json(['status' => true, 'message' => '2FA updated.', 'results' => ['enabled' => $user->two_factor_enabled]]);
    }


}
