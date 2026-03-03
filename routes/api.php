<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\{
    AuthController, 
    ContentController, 
    SearchController, 
    BookmarkController, 
    MediaController,
    ReadingProgressController,
    ReviewController
};
use App\Http\Controllers\API\V1\Admin\{
    DashboardController, 
    ContentManagementController,
    UserManagementController,
    ReportController,
    BulkUploadController
};

// Public routes
Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [AuthController::class, 'resetPassword']);
    
    // Public content
    Route::get('/content/featured', [ContentController::class, 'featured']);
    Route::get('/content', [ContentController::class, 'index']);
    Route::get('/content/{id}', [ContentController::class, 'show']);
    Route::get('/search', [SearchController::class, 'search']);
    
    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        
        // Bookmarks
        Route::get('/bookmarks', [BookmarkController::class, 'index']);
        Route::post('/bookmarks', [BookmarkController::class, 'store']);
        Route::delete('/bookmarks/{contentId}', [BookmarkController::class, 'destroy']);
        
        // Media
        Route::post('/media/upload', [MediaController::class, 'upload']);
        Route::get('/media/{id}/download', [MediaController::class, 'download']);
        
        // Reading Progress
        Route::get('/reading-progress/{contentId}', [ReadingProgressController::class, 'show']);
        Route::put('/reading-progress/{contentId}', [ReadingProgressController::class, 'update']);
        Route::get('/history', [ReadingProgressController::class, 'history']);
        
        // Reviews
        Route::get('/content/{contentId}/reviews', [ReviewController::class, 'index']);
        Route::post('/content/{contentId}/reviews', [ReviewController::class, 'store']);
        
        // Admin routes
        Route::prefix('admin')->group(function () {
            Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
            Route::get('/content', [ContentManagementController::class, 'index']);
            Route::post('/content', [ContentManagementController::class, 'store']);
            Route::put('/content/{id}', [ContentManagementController::class, 'update']);
            Route::post('/content/{id}/publish', [ContentManagementController::class, 'publish']);
            Route::delete('/content/{id}', [ContentManagementController::class, 'destroy']);
            
            // User Management
            Route::get('/users', [UserManagementController::class, 'index']);
            Route::post('/users', [UserManagementController::class, 'store']);
            Route::put('/users/{id}', [UserManagementController::class, 'update']);
            Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
            
            // Reports
            Route::get('/reports/downloads', [ReportController::class, 'downloads']);
            Route::get('/reports/popular', [ReportController::class, 'popular']);
            Route::get('/reports/activity', [ReportController::class, 'activity']);
            
            // Bulk Upload
            Route::post('/bulk-upload', [BulkUploadController::class, 'upload']);
        });
    });
});
