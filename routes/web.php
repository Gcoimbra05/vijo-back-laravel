<?php

use App\Http\Controllers\Admin\DashboardController;
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
use App\Http\Controllers\VideoTypeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('images/{filename}', function (Request $request, $filename) {
    return MediaStorageController::handlePublicFiles($request, 'images', $filename);
});
Route::get('videos/{filename}', function (Request $request, $filename) {
    return MediaStorageController::handlePublicFiles($request, 'videos', $filename);
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


// Route::prefix('admin')->middleware('admin')->group(function () {
//     Route::apiResource('users', UserController::class);
//     Route::apiResource('membership-plans', MembershipPlanController::class);
//     Route::apiResource('subscriptions', SubscriptionController::class);
//     Route::apiResource('videos', VideoController::class);
//     Route::apiResource('video-requests', VideoRequestController::class);
//     Route::apiResource('catalogs', CatalogController::class);
//     Route::apiResource('categories', CategoryController::class);
//     Route::apiResource('contacts', ContactController::class);
//     Route::apiResource('groups', GroupController::class);
//     Route::apiResource('referral-codes', ReferralCodeController::class);
//     Route::apiResource('affiliates', AffiliateController::class);
//     Route::apiResource('llm-templates', LlmTemplateController::class);
// });
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminLoginController::class, 'showLoginForm'])->name('home');
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login']);
    Route::get('/validate-otp', [AdminLoginController::class, 'showOtpForm'])->name('validate-otp');
    Route::post('/validate-otp', [AdminLoginController::class, 'processOtp']);

    // Rotas protegidas por middleware
    // Route::middleware('admin')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // admin/users
        Route::get('users', [UserController::class, 'adminIndex'])->name('users.adminIndex');
        Route::get('users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::get('users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::get('users/{id}/journal-history', [UserController::class, 'journalHistoryView'])->name('users.journalHistory');
        Route::get('users/{id}/auditLogs', [UserController::class, 'auditLogsView'])->name('users.auditLogs');
        Route::resource('users', UserController::class)->except(['index']);

        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');


        // catalogs routes
        // Route::resource('catalogs', CatalogController::class)->except(['index']);
        // Route::get('catalogs', [CatalogController::class, 'catalogsIndex'])->name('catalogs.list');
        // Route::get('catalog/add', [CatalogController::class, 'add'])->name('catalogs.add');


        // Video Types
        Route::resource('journal_types', VideoTypeController::class)->except(['index']);
        Route::get('journal_types', [VideoTypeController::class, 'journalTypesIndex'])->name('videoTypes.list');
        Route::get('journal_type/add', [VideoTypeController::class, 'add'])->name('videoTypes.form');

        Route::get('journal_type/edit/{id}', [VideoTypeController::class, 'edit'])->name('videoTypes.edit');
        Route::get('journal_type/deactivate/{id}', [VideoTypeController::class, 'deactivate'])->name('videoTypes.deactivate');
        Route::get('journal_type/activate/{id}', [VideoTypeController::class, 'activate'])->name('videoTypes.activate');
        Route::get('journal_type/delete/{id}', [VideoTypeController::class, 'destroy'])->name('videoTypes.destroy');


        // Journal Categories
        Route::resource('journal_categories', CategoryController::class)->except(['index']);
        Route::get('journal_categories', [CategoryController::class, 'index'])->name('journalCategories.list');
        Route::get('journal_category/add', [CategoryController::class, 'add'])->name('journalCategories.form');
        Route::get('journal_category/edit/{id}', [CategoryController::class, 'edit'])->name('journalCategories.edit');
        Route::get('journal_category/deactivate/{id}', [CategoryController::class, 'deactivate'])->name('journalCategories.deactivate');
        Route::get('journal_category/activate/{id}', [CategoryController::class, 'activate'])->name('journalCategories.activate');
        Route::get('journal_category/delete/{id}', [CategoryController::class, 'destroy'])->name('journalCategories.destroy');
        // });
});

// // Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
// Route::post('/admin/login', [AdminLoginController::class, 'login']);
// Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('logout');

// Fallback Route
Route::fallback(function () {
    return response()->json([
        'message' => 'Route not found.',
        'status' => false,
    ], 404);
});
