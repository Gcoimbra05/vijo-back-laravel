<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\ReferralCodeController;
use App\Http\Controllers\CatalogAnswerController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
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
use App\Services\CredScore\CredScoreService;
use App\Services\Emlo\EmloInsights\EmloInsightsService;

Route::prefix('v2')->middleware(ForceJsonResponse::class)->group(function () {
    Route::post('/resend_2fa', [TwoFactorAuthController::class, 'resend2fa']);

    Route::prefix('auth')->group(function () {
        Route::post('/sign-up', [UserController::class, 'store']);
        Route::post('/logout', [UserController::class, 'logout']);
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
    Route::get('shared-video-details/{id}', [VideoRequestController::class, 'shareJournalDetails']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::get('dashboard', [UserController::class, 'getDashboardData']);
        Route::post('update-guided-tours', [UserController::class, 'updateGuidedTour']);
        Route::get('subscription-plans', [UserController::class, 'getSubscriptionPlans']);

        Route::apiResource('membership-plans', MembershipPlanController::class);
        Route::apiResource('subscriptions', SubscriptionController::class);
        Route::apiResource('videos', VideoController::class);
        Route::apiResource('video-requests', VideoRequestController::class);
        Route::get('video-galleries', [VideoRequestController::class, 'getVideoGalleries']);
        Route::get('video-detail/{id}', [VideoRequestController::class, 'getVideoDetail']);
        Route::post('make-request', [VideoRequestController::class, 'makeVideoRequest']);
        Route::post('share-video-contacts', [VideoRequestController::class, 'shareVideoToContactsAndGroups']);
        Route::post('send-reminder', [VideoRequestController::class, 'sendReminder']);
        Route::post('unshare-video', [VideoRequestController::class, 'unshareVideoRequest']);
        Route::get('request-details/{id}', [VideoRequestController::class, 'getRequestDetails']);
        Route::get('response-request-details/{id}', [VideoRequestController::class, 'getResponseRequestDetails']);

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


        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('contacts', ContactController::class);
        Route::post('contacts/multiple', [ContactController::class, 'createMultiple']);
        Route::apiResource('groups', GroupController::class);
        Route::delete('groups/{group}/contacts/{contact}', [GroupController::class, 'removeContact']);

        Route::apiResource('referral-codes', ReferralCodeController::class);

        // need to make these admin only
        Route::apiResource('affiliates', AffiliateController::class);
        Route::apiResource('llm-templates', LlmTemplateController::class);

        Route::post('stripe/checkout-session', [StripeWebhookController::class, 'createCheckoutSession']);

        Route::get('cred-score/{request_id}', [CredScoreController::class, 'getCredScore']);

        Route::prefix('emlo-response')->group(function () {
            Route::get('get-emotion-insights/{param}', [EmloResponseController::class, 'getInsights']);

            Route::get('{request_id}/{param}/compare', [RuleEvaluationController::class, 'evaluateRules']);
            Route::get('{param}/specification', [EmloResponseParamSpecsController::class, 'showByParamName']);
            Route::get('{request_id}/{param}', [EmloResponseController::class, 'getParamValueByRequestId']);
        });

        Route::get('/insights-v2', [EmloInsightsService::class, 'getInsightsDataV2'])
            ->name('api.v2.insights.v2');

        Route::get('/insights-v2/secondaryMetrics', [EmloInsightsService::class, 'getInsightsDataV2'])
            ->name('api.v2.insights.v2.secondary-metrics');
        Route::get('/insights-v2/vijos', [CredScoreService::class, 'getAllLatestCredScoreData']);

        Route::get('/stripe/customer-portal', [StripeWebhookController::class, 'getCustomerPortal']);
    });

    // Stripe Webhook
    Route::post('stripe/webhook', [StripeWebhookController::class, 'handle']);
});
