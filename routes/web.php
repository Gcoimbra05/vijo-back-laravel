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
use App\Http\Controllers\PlatformTextController;
use App\Http\Controllers\ReferralCodeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\VideoRequestController;
use App\Http\Controllers\VideoTypeController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\JobBatchesController;
use App\Http\Controllers\FailedJobsController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\UserFeedbackController;
use App\Http\Controllers\UserFeedbackReplyController;
use App\Http\Controllers\VijoPlansController;

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
    return MediaStorageController::streamWithPresignedUrl($request, 'videos', $filename);
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

Route::prefix('admin')->name('admin.')->group(function () {
    // Public routes (login)
    Route::get('/', [AdminLoginController::class, 'showLoginForm'])->name('home');
    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('login');
    Route::get('/validate-otp', [AdminLoginController::class, 'showOtpForm'])->name('validate-otp');
    Route::post('/validate-otp', [AdminLoginController::class, 'processOtp'])->name('validate-otp.post');

    Route::get('/forgot-password', [AdminLoginController::class, 'forgotview'])->name('forgot.view');
    Route::post('/forgot-password', [AdminLoginController::class, 'forgot'])->name('password.forgot');

    Route::get('/validate-token', [AdminLoginController::class, 'showvalidatetoken'])->name('validatetoken.show');
    Route::post('/validate-token', [AdminLoginController::class, 'validatetoken'])->name('password.validatetoken');

    Route::get('/reset-password', [AdminLoginController::class, 'showresetpassword'])->name('resetpassword.show');
    Route::post('/reset-password', [AdminLoginController::class, 'resetpassword'])->name('password.resetpassword');
    
    // Protected routes (require admin authentication)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Users Management
        Route::get('users', [UserController::class, 'adminIndex'])->name('users.index');
        Route::get('users/{id}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');
        Route::get('users/{id}/activate', [UserController::class, 'activate'])->name('users.activate');
        Route::get('users/{id}/journal-history', [UserController::class, 'journalHistoryView'])->name('users.journalHistory');
        Route::get('users/{id}/auditLogs', [UserController::class, 'auditLogsView'])->name('users.auditLogs');
        Route::get('users/{userId}/contacts', [UserController::class, 'contactsByUser'])->name('users.contacts');
        Route::resource('users', UserController::class)->except(['index']);

        // User Logins
        Route::prefix('userlogin')->name('userlogin.')->group(function () {
            Route::get('/', [UserLoginController::class, 'index'])->name('index');
            Route::get('/create', [UserLoginController::class, 'create'])->name('create');
            Route::post('/', [UserLoginController::class, 'store'])->name('store');
            Route::get('/show/{id}', [UserLoginController::class, 'show'])->name('show');
            Route::put('/{id}', [UserLoginController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [UserLoginController::class, 'destroy'])->name('delete');
        });

        // User Feedback
        Route::prefix('userfeedbacks')->name('userfeedbacks.')->group(function () {
            Route::get('/', [UserFeedbackController::class, 'index'])->name('index');
            Route::get('/create', [UserFeedbackController::class, 'create'])->name('create');
            Route::post('/', [UserFeedbackController::class, 'store'])->name('store');
            Route::get('/show/{id}', [UserFeedbackController::class, 'show'])->name('show');
            Route::put('/{id}', [UserFeedbackController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [UserFeedbackController::class, 'destroy'])->name('delete');
            Route::get('/read/{id}', [UserFeedbackController::class, 'read'])->name('read');
            Route::get('/unread/{id}', [UserFeedbackController::class, 'unread'])->name('unread');
        });

        // User Feedback Reply
        Route::prefix('userfeedbackreply')->name('userfeedbackreply.')->group(function () {
            Route::get('/', [UserFeedbackReplyController::class, 'index'])->name('index');
            Route::get('/create', [UserFeedbackReplyController::class, 'create'])->name('create');
            Route::post('/', [UserFeedbackReplyController::class, 'store'])->name('store');
            Route::get('/show/{id}', [UserFeedbackReplyController::class, 'show'])->name('show');
        });

        // Email Templates
        Route::prefix('emailtemplate')->name('emailtemplate.')->group(function () {
            Route::get('/', [EmailTemplateController::class, 'index'])->name('index');
            Route::get('/create', [EmailTemplateController::class, 'create'])->name('create');
            Route::post('/', [EmailTemplateController::class, 'store'])->name('store');
            Route::get('/show/{id}', [EmailTemplateController::class, 'show'])->name('show');
            Route::put('/{id}', [EmailTemplateController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [EmailTemplateController::class, 'destroy'])->name('delete');
            Route::get('/activate/{id}', [EmailTemplateController::class, 'activate'])->name('activate');
            Route::get('/deactivate/{id}', [EmailTemplateController::class, 'deactivate'])->name('deactivate');
        });

        // Platform Texts
        Route::prefix('platformtext')->name('platformtext.')->group(function () {
            Route::get('/', [PlatformTextController::class, 'index'])->name('index');
            Route::get('/create', [PlatformTextController::class, 'create'])->name('create');
            Route::post('/', [PlatformTextController::class, 'store'])->name('store');
            Route::get('/show/{id}', [PlatformTextController::class, 'show'])->name('show');
            Route::put('/{id}', [PlatformTextController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [PlatformTextController::class, 'destroy'])->name('delete');
            Route::get('/activate/{id}', [PlatformTextController::class, 'activate'])->name('activate');
            Route::get('/deactivate/{id}', [PlatformTextController::class, 'deactivate'])->name('deactivate');
        });

        // Catalogs (Resource)
        Route::resource('catalogs', CatalogController::class);

        // Video Types (Journal Types)
        Route::get('journal_types', [VideoTypeController::class, 'journalTypesIndex'])->name('journalTypes.index');
        Route::get('journal_type/add', [VideoTypeController::class, 'add'])->name('journalTypes.add');
        Route::get('journal_type/edit/{id}', [VideoTypeController::class, 'edit'])->name('journalTypes.edit');
        Route::get('journal_type/deactivate/{id}', [VideoTypeController::class, 'deactivate'])->name('journalTypes.deactivate');
        Route::get('journal_type/activate/{id}', [VideoTypeController::class, 'activate'])->name('journalTypes.activate');
        Route::get('journal_type/delete/{id}', [VideoTypeController::class, 'destroy'])->name('journalTypes.destroy');
        Route::resource('journal_types', VideoTypeController::class)->except(['index', 'show', 'edit', 'destroy']);

        // Journal Categories
        Route::get('journal_categories', [CategoryController::class, 'index'])->name('journalCategories.index');
        Route::get('journal_category/add', [CategoryController::class, 'add'])->name('journalCategories.add');
        Route::get('journal_category/edit/{id}', [CategoryController::class, 'edit'])->name('journalCategories.edit');
        Route::get('journal_category/deactivate/{id}', [CategoryController::class, 'deactivate'])->name('journalCategories.deactivate');
        Route::get('journal_category/activate/{id}', [CategoryController::class, 'activate'])->name('journalCategories.activate');
        Route::get('journal_category/delete/{id}', [CategoryController::class, 'destroy'])->name('journalCategories.destroy');
        Route::resource('journal_categories', CategoryController::class)->except(['index', 'show', 'edit', 'destroy']);

        // Catalog Custom Routes (in addition to resource routes)
        Route::prefix('catalog')->name('catalog.')->group(function () {
            Route::get('/activate/{id}', [CatalogController::class, 'activate'])->name('activate');
            Route::get('/deactivate/{id}', [CatalogController::class, 'deactivate'])->name('deactivate');
        });

        // Tags
        Route::prefix('tags')->name('tags.')->group(function () {
            Route::get('/', [TagController::class, 'index'])->name('index');
            Route::get('/add', [TagController::class, 'add'])->name('add');
            Route::post('/', [TagController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [TagController::class, 'edit'])->name('edit');
            Route::put('/{id}', [TagController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [TagController::class, 'destroy'])->name('delete');
            Route::get('/deactivate/{id}', [TagController::class, 'deactivate'])->name('deactivate');
            Route::get('/activate/{id}', [TagController::class, 'activate'])->name('activate');
        });

        // Memberships
        Route::prefix('memberships')->name('memberships.')->group(function () {
            Route::get('/', [MembershipPlanController::class, 'index'])->name('index');
            Route::get('/add', [MembershipPlanController::class, 'add'])->name('add');
            Route::post('/', [MembershipPlanController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [MembershipPlanController::class, 'show'])->name('edit');
            Route::put('/{id}', [MembershipPlanController::class, 'update'])->name('update');
            Route::get('/delete/{id}', [MembershipPlanController::class, 'destroy'])->name('delete');
            Route::get('/deactivate/{id}', [MembershipPlanController::class, 'deactivate'])->name('deactivate');
            Route::get('/activate/{id}', [MembershipPlanController::class, 'activate'])->name('activate');
        });
    
        Route::prefix('vijoplans')->group(function () {
            Route::get('/', [VijoPlansController::class, 'index'])->name('vijoplan.index');
            Route::post('/', [VijoPlansController::class, 'store'])->name('vijoplan.store');
            Route::put('/{id}', [VijoPlansController::class, 'update'])->name('vijoplan.update');
            Route::get('/delete/{id}', [VijoPlansController::class, 'destroy'])->name('vijoplan.delete');
        });
    }); // End of protected admin routes
}); // End of admin prefix

// Fallback Route
Route::fallback(function () {
    return response()->json([
        'message' => 'Route not found.',
        'status' => false,
    ], 404);
});
