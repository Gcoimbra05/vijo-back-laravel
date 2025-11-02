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
        Route::get('admin/users/{user:name}/edit', [UserController::class, 'edit']);
        Route::get('users/{userId}/contacts', [UserController::class, 'contactsByUser'])->name('users.contacts');

        // User Logins
        Route::prefix('userlogin')->group(function () {
            Route::get('/', [UserLoginController::class, 'index'])->name('userlogin.index');
            Route::get('/create', [UserLoginController::class, 'create'])->name('userlogin.create');
            Route::post('/', [UserLoginController::class, 'store'])->name('userlogin.store');
            Route::get('/show/{id}', [UserLoginController::class, 'show'])->name('userlogin.show');
            Route::put('/{id}', [UserLoginController::class, 'update'])->name('userlogin.update');
            Route::get('/delete/{id}', [UserLoginController::class, 'destroy'])->name('userlogin.delete');
        });

        // User Feedback
        Route::prefix('userfeedbacks')->group(function () {
            Route::get('/', [UserFeedbackController::class, 'index'])->name('userfeedbacks.index');
            Route::get('/create', [UserFeedbackController::class, 'create'])->name('userfeedbacks.create');
            Route::post('/', [UserFeedbackController::class, 'store'])->name('userfeedbacks.store');
            Route::get('/show/{id}', [UserFeedbackController::class, 'show'])->name('userfeedbacks.show');
            Route::put('/{id}', [UserFeedbackController::class, 'update'])->name('userfeedbacks.update');
            Route::get('/delete/{id}', [UserFeedbackController::class, 'destroy'])->name('userfeedbacks.delete');

            Route::get('/read/{id}', [UserFeedbackController::class, 'read'])->name('userfeedbacks.read');
            Route::get('/unread/{id}', [UserFeedbackController::class, 'unread'])->name('userfeedbacks.unread');
        });

        // User Feedback Reply
        Route::prefix('userfeedbackreply')->group(function () {
            Route::get('/', [UserFeedbackReplyController::class, 'index'])->name('userfeedbackreply.index');
            Route::get('/create', [UserFeedbackReplyController::class, 'create'])->name('userfeedbackreply.create');
            Route::post('/', [UserFeedbackReplyController::class, 'store'])->name('userfeedbackreply.store');
            Route::get('/show/{id}', [UserFeedbackReplyController::class, 'show'])->name('userfeedbackreply.show');
        });

        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');

        // email templates
        Route::prefix('emailtemplate')->group(function () {
            Route::get('/', [EmailTemplateController::class, 'index'])->name('emailtemplate.index');
            Route::get('/create', [EmailTemplateController::class, 'create'])->name('emailtemplate.create');
            Route::post('/', [EmailTemplateController::class, 'store'])->name('emailtemplate.store');
            Route::get('/show/{id}', [EmailTemplateController::class, 'show'])->name('emailtemplate.show');
            Route::put('/{id}', [EmailTemplateController::class, 'update'])->name('emailtemplate.update');
            Route::get('/delete/{id}', [EmailTemplateController::class, 'destroy'])->name('emailtemplate.delete');

            Route::get('/activate/{id}', [EmailTemplateController::class, 'activate'])->name('emailtemplate.activate');
            Route::get('/deactivate/{id}', [EmailTemplateController::class, 'deactivate'])->name('emailtemplate.deactivate');
        });

        // Platform Texts
        Route::prefix('platformtext')->group(function () {
            Route::get('/', [PlatformTextController::class, 'index'])->name('platformtext.index');
            Route::get('/create', [PlatformTextController::class, 'create'])->name('platformtext.create');
            Route::post('/', [PlatformTextController::class, 'store'])->name('platformtext.store');
            Route::get('/show/{id}', [PlatformTextController::class, 'show'])->name('platformtext.show');
            Route::put('/{id}', [PlatformTextController::class, 'update'])->name('platformtext.update');
            Route::get('/delete/{id}', [PlatformTextController::class, 'destroy'])->name('platformtext.delete');

            Route::get('/activate/{id}', [PlatformTextController::class, 'activate'])->name('platformtext.activate');
            Route::get('/deactivate/{id}', [PlatformTextController::class, 'deactivate'])->name('platformtext.deactivate');
        });


        // catalogs routes
        Route::resource('catalogs', CatalogController::class); // ->except(['index']);
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

        // Platform Texts
        Route::resource('platform_texts', PlatformTextController::class);
    
        Route::prefix('catalog')->group(function () {
            Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
            Route::get('/add', [CatalogController::class, 'add'])->name('catalog.add');
            Route::post('/', [CatalogController::class, 'store'])->name('catalog.store');
            Route::get('/edit/{id}', [CatalogController::class, 'edit'])->name('catalog.edit');
            Route::put('/{id}', [CatalogController::class, 'update'])->name('catalog.update');
            Route::get('/delete/{id}', [CatalogController::class, 'destroy'])->name('catalog.delete');

            Route::get('/activate/{id}', [CatalogController::class, 'activate'])->name('catalog.activate');
            Route::get('/deactivate/{id}', [CatalogController::class, 'deactivate'])->name('catalog.deactivate');
        });

        Route::prefix('tags')->group(function () {
            Route::get('/', [TagController::class, 'index'])->name('tag.index');
            Route::get('/add', [TagController::class, 'add'])->name('tag.add');
            Route::post('/', [TagController::class, 'store'])->name('tag.store');
            Route::get('/edit/{id}', [TagController::class, 'edit'])->name('tag.edit');
            Route::put('/{id}', [TagController::class, 'update'])->name('tag.update');
            Route::get('/delete/{id}', [TagController::class, 'destroy'])->name('tag.delete');

            Route::get('admin/tags/deactivate/{id}', [TagController::class, 'deactivate'])->name('tag.deactivate');
            Route::get('admin/tags/activate/{id}', [TagController::class, 'activate'])->name('tag.activate');
        });

        Route::prefix('memberships')->group(function () {
            Route::get('/', [MembershipPlanController::class, 'index'])->name('membership.index');
            Route::get('/add', [MembershipPlanController::class, 'add'])->name('membership.add');
            Route::post('/', [MembershipPlanController::class, 'store'])->name('membership.store');
            Route::get('/edit/{id}', [MembershipPlanController::class, 'show'])->name('membership.edit');
            Route::put('/{id}', [MembershipPlanController::class, 'update'])->name('membership.update');
            Route::get('/delete/{id}', [MembershipPlanController::class, 'destroy'])->name('membership.delete');

            Route::get('admin/memberships/deactivate/{id}', [MembershipPlanController::class, 'deactivate'])->name('membership.deactivate');
            Route::get('admin/memberships/activate/{id}', [MembershipPlanController::class, 'activate'])->name('membership.activate');
        });

        // Jobs
        Route::prefix('job')->group(function () {
            Route::get('/', [JobController::class, 'index'])->name('job.index');
            Route::get('/create', [JobController::class, 'create'])->name('job.create');
            Route::post('/', [JobController::class, 'store'])->name('job.store');
            Route::get('/show/{id}', [JobController::class, 'show'])->name('job.show');
            Route::put('/{id}', [JobController::class, 'update'])->name('job.update');
            Route::get('/delete/{id}', [JobController::class, 'destroy'])->name('job.delete');
        });

        // Jobs Batches
        Route::prefix('job_batches')->group(function () {
            Route::get('/', [JobBatchesController::class, 'index'])->name('job_batches.index');
            Route::get('/create', [JobBatchesController::class, 'create'])->name('job_batches.create');
            Route::post('/', [JobBatchesController::class, 'store'])->name('job_batches.store');
            Route::get('/show/{id}', [JobBatchesController::class, 'show'])->name('job_batches.show');
            Route::put('/{id}', [JobBatchesController::class, 'update'])->name('job_batches.update');
            Route::get('/delete/{id}', [JobBatchesController::class, 'destroy'])->name('job_batches.delete');
        });

        // Failed Jobs
        Route::prefix('failed_jobs')->group(function () {
            Route::get('/', [FailedJobsController::class, 'index'])->name('failed_jobs.index');
            Route::get('/create', [FailedJobsController::class, 'create'])->name('failed_jobs.create');
            Route::post('/', [FailedJobsController::class, 'store'])->name('failed_jobs.store');
            Route::get('/show/{id}', [FailedJobsController::class, 'show'])->name('failed_jobs.show');
            Route::put('/{id}', [FailedJobsController::class, 'update'])->name('failed_jobs.update');
            Route::get('/delete/{id}', [FailedJobsController::class, 'destroy'])->name('failed_jobs.delete');
        });
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
