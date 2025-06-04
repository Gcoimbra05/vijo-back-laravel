<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\CatalogAnswerController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EmloController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoRequestController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\VideoController;
use App\Http\Middleware\ForceJsonResponse;
use App\Services\Emlo\EmloResponseService;
use App\Http\Controllers\EmloResponseParamSpecsController;
use App\Http\Controllers\LlmTemplateController;
use App\Http\Controllers\RuleEvaluationController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;



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
    Route::post('/insights', [SettingsController::class, 'getInsights']); // chart_type=bar&view_by=days_of_week&filter_by=daily&datatype=emotion&metric1=emotion%23%231&no_zero_record=0

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::get('dashboard', [UserController::class, 'getDashboardData']);
        Route::post('update-guided-tours', [UserController::class, 'updateGuidedTour']);
        Route::get('subscription-plans', [UserController::class, 'getSubscriptionPlans']);

        Route::apiResource('affiliates', AffiliateController::class);
        Route::apiResource('llm-templates', LlmTemplateController::class);
        Route::apiResource('membership-plans', MembershipPlanController::class);
        Route::apiResource('subscriptions', SubscriptionController::class);
        Route::apiResource('videos', VideoController::class);
        Route::apiResource('video-requests', VideoRequestController::class);
        Route::get('video-galleries', [VideoRequestController::class, 'getVideoGalleries']);
        Route::get('video-detail/{id}', [VideoRequestController::class, 'getVideoDetail']);
        Route::post('make-request', [VideoRequestController::class, 'makeVideoRequest']);

        Route::post('cancel-decline-request', [VideoRequestController::class, 'cancelDeclineRecordRequest']);
        Route::post('share-video-requests', [VideoRequestController::class, 'shareVideoRequests']);
        Route::post('process-video-request/{id}', [VideoRequestController::class, 'initProcess']);
        Route::get('related-requests/{id}', [VideoRequestController::class, 'getRelatedRequests']);
        Route::post('delete-requests/{id}', [VideoRequestController::class, 'deleteVideoRequests']);

        Route::post('start-video-request', [VideoRequestController::class, 'startVideoRequest']);
        Route::post('record-video-request', [CatalogAnswerController::class, 'store']);
        Route::post('save-video-request', [VideoRequestController::class, 'saveVideoRequest']);

        Route::apiResource('catalogs', CatalogController::class);
        Route::get('catalogs-by-category/{categoryId}', [CatalogController::class, 'getCatalogsByCategory']);

        Route::apiResource('categories', CategoryController::class);
        Route::apiResource('contacts', ContactController::class);
        Route::apiResource('groups', GroupController::class);
        Route::delete('groups/{group}/contacts/{contact}', [GroupController::class, 'removeContact']);
    });

    Route::match(['get', 'post'], 'webhook', [StripeWebhookController::class, 'handle']);

    Route::prefix('emlo-response')->group(function () {
        Route::get('all', [EmloController::class, 'getAllEmloResponses']);
        Route::get('{request_id}/param/{param}/compare', [RuleEvaluationController::class, 'evaluateRules']);
        Route::get('{request_id}/param/{param}', [EmloResponseService::class, 'getEmloResponseParamValueForId']);
        Route::get('param/{param}', [EmloController::class, 'getEmloResponseParamValue']);
        Route::get('param/{param}/specification', [EmloResponseParamSpecsController::class, 'showByParamName']);
    });
    Route::apiResource('emlo-response', EmloController::class);
});
