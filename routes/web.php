<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MediaStorageController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('images/{filename}', function ($filename) {
    return MediaStorageController::handlePublicFiles('images', $filename);
});
Route::get('videos/{filename}', function ($filename) {
    return MediaStorageController::handlePublicFiles('videos', $filename);
});
Route::get('thumbnails/{filename}', function ($filename) {
    return MediaStorageController::handlePublicFiles('thumbnails', $filename);
});
Route::get('documents/{filename}', function ($filename) {
    return MediaStorageController::handlePublicFiles('documents', $filename);
});
Route::get('pdf/{filename}', function ($filename) {
    return MediaStorageController::handlePublicFiles('pdf', $filename);
});

// Fallback Route
Route::fallback(function () {
    return response()->json([
        'message' => 'Route not found.',
        'status' => false,
    ], 404);
});