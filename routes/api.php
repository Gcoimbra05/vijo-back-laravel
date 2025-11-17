<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AiAgentController;
use App\Http\Controllers\BaselineController;
use App\Http\Controllers\ReferralCodeController;
use App\Http\Controllers\CatalogAnswerController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CredScoreController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoRequestController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\VideoController;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Controllers\LlmTemplateController;
use App\Http\Controllers\RuleEvaluationController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmloResponseController;
use App\Http\Controllers\EmloResponseParamSpecsController;
use App\Http\Controllers\PlatformTextController;
use App\Http\Controllers\InsightsFilterController;
use App\Http\Controllers\InsightsController;
use App\Http\Controllers\QuickGoalController;
use App\Http\Controllers\SkipVijoController;
use App\Http\Controllers\UserFeedbackController;
use App\Services\CredScore\CredScoreService;
use App\Services\Emlo\EmloInsights\EmloInsightsService;

Route::prefix('v2')->middleware(ForceJsonResponse::class)->group(function () {
    Route::post('/resend_2fa', [TwoFactorAuthController::class, 'resend2fa']);
    Route::post('/send-reset-link', [UserController::class, 'sendResetLink']);
    Route::post('/password/reset', [UserController::class, 'resetPasswordAndLogin']);

    Route::prefix('auth')->group(function () {
        Route::post('/sign-up', [UserController::class, 'store']);
        Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
        Route::post('/reset-password', [UserController::class, 'resetPassword']);
        Route::post('/verify-email', [UserController::class, 'verifyEmail']);
        Route::post('/verify-email-resend', [UserController::class, 'resendEmailVerification']);
        Route::post('/sign-in', [TwoFactorAuthController::class, 'sendCode']);
        Route::post('/validate_2fa', [TwoFactorAuthController::class, 'verifyCode']);
        Route::post('/refresh-token', [TwoFactorAuthController::class, 'refreshToken']);
        Route::post('/validate-token', [TwoFactorAuthController::class, 'validateToken']);
    });

    // fake routes (Static data)
    Route::get('/countries', [SettingsController::class, 'getCountries']);
    Route::get('/onboarding-contents', [SettingsController::class, 'getOnboardingContent']);
    Route::get('/information-contents', [SettingsController::class, 'getInformationContent']);
    Route::get('/static-pages', [SettingsController::class, 'getStaticPages']);

    // video request details for shared links
    Route::get('shared-video-details/{id}', [VideoRequestController::class, 'shareJournalDetails']);
    Route::get('response-request-details/{id}', [VideoRequestController::class, 'getResponseRequestDetails']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [UserController::class, 'logout']);

        Route::apiResource('users', UserController::class);

        Route::post('user/feedback', [UserFeedbackController::class, 'store']);
        Route::delete('user/delete-account', [UserController::class, 'deleteAccount']);

        Route::get('dashboard', [UserController::class, 'getDashboardData']);
        Route::get('emotional-snapshot', [EmloResponseController::class, 'getEmotionalSnapshot']);
        Route::post('update-guided-tours', [UserController::class, 'updateGuidedTour']);
        Route::get('subscription-plans', [UserController::class, 'getSubscriptionPlans']);

        Route::get('membership-plans', [MembershipPlanController::class, 'index']);
        Route::get('membership-plans/{membership_plan}', [MembershipPlanController::class, 'show']);
        /* Route::apiResource('subscriptions', SubscriptionController::class);
        Route::apiResource('videos', VideoController::class); */
        Route::post('video-requests', [VideoRequestController::class, 'store']);
        Route::put('video-requests/{video_request}', [VideoRequestController::class, 'update']);
        Route::get('video-requests/{video_request}', [VideoRequestController::class, 'show']);

        Route::post('skip-vijo', [SkipVijoController::class, 'store']);

        Route::get('video-galleries', [VideoRequestController::class, 'getVideoGalleries']);
        Route::get('video-detail/{id}', [VideoRequestController::class, 'getVideoDetail']);
        Route::get('video-results/{id}', [VideoRequestController::class, 'getVideoResults']);
        Route::post('make-request', [VideoRequestController::class, 'makeVideoRequest']);

        Route::post('share-video-contacts', [VideoRequestController::class, 'shareVideoToContactsAndGroups']);

        Route::post('send-reminder', [VideoRequestController::class, 'sendReminder']);
        Route::post('unshare-video', [VideoRequestController::class, 'unshareVideoRequest']);
        Route::get('request-details/{id}', [VideoRequestController::class, 'getRequestDetails']);

        Route::post('cancel-decline-request', [VideoRequestController::class, 'cancelDeclineRecordRequest']);
        Route::post('share-video-requests', [VideoRequestController::class, 'shareVideoRequests']);
        Route::post('process-video-request/{id}', [VideoRequestController::class, 'initProcess']);

        Route::get('related-requests/{id}', [VideoRequestController::class, 'getRelatedRequests']);
        Route::delete('delete-requests/{id}', [VideoRequestController::class, 'deleteVideoRequests']);

        Route::post('start-video-request', [VideoRequestController::class, 'startVideoRequest']);
        Route::post('record-video-request', [CatalogAnswerController::class, 'store']);
        Route::post('save-video-request', [VideoRequestController::class, 'saveVideoRequest']);

        Route::apiResource('catalogs', CatalogController::class);
        Route::get('catalogs-by-category/{categoryId}', [CatalogController::class, 'getCatalogsByCategory']);

        Route::apiResource('insights-filters', InsightsFilterController::class);

        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('contacts', ContactController::class);
        Route::post('contacts/multiple', [ContactController::class, 'createMultiple']);
        Route::apiResource('groups', GroupController::class);
        Route::delete('groups/{group}/contacts/{contact}', [GroupController::class, 'removeContact']);

        Route::apiResource('referral-codes', ReferralCodeController::class);

        Route::apiResource('baselines', BaselineController::class);

        // need to make these admin only
        Route::apiResource('affiliates', AffiliateController::class);
        Route::apiResource('llm-templates', LlmTemplateController::class);

        Route::post('stripe/checkout-session', [StripeWebhookController::class, 'createCheckoutSession']);

        Route::get('cred-score/{request_id}', [CredScoreController::class, 'getCredScore']);

        Route::prefix('emlo-response')->group(function () {
            Route::get('{param}/specification', [EmloResponseParamSpecsController::class, 'showByParamName']);
            Route::get('{request_id}/{param}', [EmloResponseController::class, 'getParamValueByRequestId']);
        });

        Route::prefix('ai-agent')->group(function () {
            Route::get('/emotion-insights/{emotion_name}', [AiAgentController::class, 'getSingleParamEmotionalInsights']);

        });

        Route::get('/insights-v2', [InsightsController::class, 'getInsights'])
            ->name('api.v2.insights.v2');

        Route::get('/insights-v2/secondaryMetrics', [InsightsController::class, 'getInsights'])
            ->name('api.v2.insights.v2.secondary-metrics');
        Route::get('/insights-v2/vijos', [CredScoreService::class, 'getAllLatestCredScoreData']);
        //Route::get('/insights-v2/secondaryMetrics', [EmloInsightsService::class, 'getInsightsDataV2'])->name('api.v2.insights.v2.secondary-metrics');

        Route::get('/stripe/customer-portal', [StripeWebhookController::class, 'getCustomerPortal']);

        Route::apiResource('coupons', CouponController::class);

        // Profile and Security
        Route::post('/validate-profile', [UserController::class, 'validateProfile']);
        Route::post('/update-profile', [UserController::class, 'updateProfile']);
        Route::post('/save-new-password', [UserController::class, 'saveNewPassword']);
        Route::post('/update-2fa', [UserController::class, 'updateTwoFactor']);

        Route::get('quick-goals', [QuickGoalController::class, 'index']);
        Route::post('quick-goals', [QuickGoalController::class, 'store']);
        Route::put('quick-goals', [QuickGoalController::class, 'update']);
        Route::get('quick-goals/{quick_goal}', [QuickGoalController::class, 'show']);
        Route::delete('quick-goals/{quick_goal}', [QuickGoalController::class, 'destroy']);
    });

    // Platform Texts
    Route::get('platform-texts', [PlatformTextController::class, 'index']);
    Route::get('platform-texts/{id}', [PlatformTextController::class, 'show']);

    // Stripe Webhook
    Route::post('stripe/webhook', [StripeWebhookController::class, 'handle']);
});
