<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\MembershipPlanController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoRequestController;

Route::get('/', function () {
    return view('welcome');
});

/* // User Routes
Route::resource('users', UserController::class);

// Membership Plan Routes
Route::resource('membership-plans', MembershipPlanController::class);

// Subscription Routes
Route::resource('subscriptions', SubscriptionController::class);

// Affiliate Routes
Route::resource('affiliates', AffiliateController::class);

// Catalog Routes
Route::resource('catalogs', CatalogController::class);

// Contact Routes
Route::resource('contacts', ContactController::class);

// Group Routes
Route::resource('groups', GroupController::class);

// Video Request Routes
Route::resource('video-requests', VideoRequestController::class); */

// Fallback Route
Route::fallback(function () {
    return response()->json([
        'message' => 'Route not found.',
        'status' => false,
    ], 404);
});