<?php

use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\LlmTemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaStorageController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\ReferralCodeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\VideoRequestController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('images/{filename}', function (Request $request, $filename) {
    return MediaStorageController::handlePublicFiles($request, 'images', $filename);
});

Route::get('videos/{filename}', function (Request $request, $filename) {
    return MediaStorageController::handlePublicFiles($request, 'videos', $filename);
});

Route::get('videos-test/{filename}', function (Request $request, $filename) {
    return MediaStorageController::handlePublicFilesNew($request, 'videos', $filename);
});

Route::get('thumbnails/{filename}', function (Request $request, $filename) {
    return MediaStorageController::handlePublicFiles($request, 'thumbnails', $filename);
});

Route::get('documents/{filename}', function (Request $request, $filename) {
    return MediaStorageController::handlePublicFiles($request, 'documents', $filename);
});

Route::get('pdf/{filename}', function (Request $request, $filename) {
    return MediaStorageController::handlePublicFiles($request, 'pdf', $filename);
});

Route::prefix('admin')->middleware('admin')->group(function () {
    Route::apiResource('users', UserController::class)->names('admin.users');
    Route::apiResource('membership-plans', MembershipPlanController::class)->names('admin.membership-plans');
    Route::apiResource('subscriptions', SubscriptionController::class)->names('admin.subscriptions');
    Route::apiResource('videos', VideoController::class)->names('admin.videos');
    Route::apiResource('video-requests', VideoRequestController::class)->names('admin.video-requests');
    Route::apiResource('catalogs', CatalogController::class)->names('admin.catalogs');
    Route::apiResource('categories', CategoryController::class)->names('admin.categories');
    Route::apiResource('contacts', ContactController::class)->names('admin.contacts');
    Route::apiResource('groups', GroupController::class)->names('admin.groups');
    Route::apiResource('referral-codes', ReferralCodeController::class)->names('admin.referral-codes');
    Route::apiResource('affiliates', AffiliateController::class)->names('admin.affiliates');
    Route::apiResource('llm-templates', LlmTemplateController::class)->names('admin.llm-templates');
});

Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// Fallback Route
Route::fallback(function () {
    return response()->json([
        'message' => 'Route not found.',
        'status' => false,
    ], 404);
});
