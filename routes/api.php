<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HostingPlanController;
use App\Http\Controllers\PlanFeatureController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TestimonialController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication Routes
Route::group(['prefix' => 'auth'], function () {
    // Public routes (no authentication required)
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    
    // Protected routes (require authentication)
    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Site Settings API Routes
Route::prefix('settings')->group(function () {
    Route::get('/', [SiteSettingController::class, 'index']);
    Route::get('/{key}', [SiteSettingController::class, 'show']);
    Route::post('/', [SiteSettingController::class, 'store']);
    Route::put('/{key}', [SiteSettingController::class, 'update']);
    Route::delete('/{key}', [SiteSettingController::class, 'destroy']);
});

// Service Categories API Routes
Route::prefix('service-categories')->group(function () {
    Route::get('/', [ServiceCategoryController::class, 'index']);
    Route::get('/{id}', [ServiceCategoryController::class, 'show']);
    Route::post('/', [ServiceCategoryController::class, 'store']);
    Route::put('/{id}', [ServiceCategoryController::class, 'update']);
    Route::delete('/{id}', [ServiceCategoryController::class, 'destroy']);
    Route::patch('/{id}/toggle-active', [ServiceCategoryController::class, 'toggleActive']);
});

// Hosting Plans API Routes
Route::prefix('hosting-plans')->group(function () {
    Route::get('/', [HostingPlanController::class, 'index']);
    Route::get('/{id}', [HostingPlanController::class, 'show']);
    Route::post('/', [HostingPlanController::class, 'store']);
    Route::put('/{id}', [HostingPlanController::class, 'update']);
    Route::delete('/{id}', [HostingPlanController::class, 'destroy']);
    Route::patch('/{id}/toggle-active', [HostingPlanController::class, 'toggleActive']);
    Route::patch('/{id}/toggle-highlighted', [HostingPlanController::class, 'toggleHighlighted']);
    Route::patch('/{id}/toggle-popular', [HostingPlanController::class, 'togglePopular']);
});

// Plan Features API Routes
Route::prefix('plan-features')->group(function () {
    Route::get('/', [PlanFeatureController::class, 'index']);
    Route::get('/{id}', [PlanFeatureController::class, 'show']);
    Route::post('/', [PlanFeatureController::class, 'store']);
    Route::post('/bulk', [PlanFeatureController::class, 'bulkStore']);
    Route::put('/{id}', [PlanFeatureController::class, 'update']);
    Route::delete('/{id}', [PlanFeatureController::class, 'destroy']);
    Route::patch('/{id}/toggle-included', [PlanFeatureController::class, 'toggleIncluded']);
    Route::get('/by-plan/{planId}', [PlanFeatureController::class, 'getByPlan']);
});

// Portfolio API Routes
Route::prefix('portfolio')->group(function () {
    Route::get('/', [PortfolioController::class, 'index']);
    Route::get('/{slug}', [PortfolioController::class, 'show']);
    Route::post('/', [PortfolioController::class, 'store']);
    Route::put('/{slug}', [PortfolioController::class, 'update']);
    Route::delete('/{slug}', [PortfolioController::class, 'destroy']);
    Route::patch('/{slug}/toggle-featured', [PortfolioController::class, 'toggleFeatured']);
    Route::patch('/{slug}/toggle-published', [PortfolioController::class, 'togglePublished']);
});

// Team Members API Routes
Route::prefix('team-members')->group(function () {
    Route::get('/', [TeamMemberController::class, 'index']);
    Route::get('/{id}', [TeamMemberController::class, 'show']);
    Route::post('/', [TeamMemberController::class, 'store']);
    Route::put('/{id}', [TeamMemberController::class, 'update']);
    Route::delete('/{id}', [TeamMemberController::class, 'destroy']);
    Route::patch('/{id}/toggle-active', [TeamMemberController::class, 'toggleActive']);
    Route::patch('/{id}/toggle-verified', [TeamMemberController::class, 'toggleVerified']);
    Route::get('/by-role/{role}', [TeamMemberController::class, 'getByRole']);
});

// FAQ Categories API Routes
Route::prefix('faq-categories')->group(function () {
    Route::get('/', [FaqCategoryController::class, 'index']);
    Route::get('/active-with-faqs', [FaqCategoryController::class, 'getActiveWithFaqs']);
    Route::get('/{id}', [FaqCategoryController::class, 'show']);
    Route::post('/', [FaqCategoryController::class, 'store']);
    Route::put('/{id}', [FaqCategoryController::class, 'update']);
    Route::delete('/{id}', [FaqCategoryController::class, 'destroy']);
    Route::patch('/{id}/toggle-active', [FaqCategoryController::class, 'toggleActive']);
    Route::post('/reorder', [FaqCategoryController::class, 'reorder']);
});

// FAQs API Routes
Route::prefix('faqs')->group(function () {
    Route::get('/', [FaqController::class, 'index']);
    Route::get('/featured', [FaqController::class, 'getFeatured']);
    Route::get('/public', [FaqController::class, 'getPublic']);
    Route::get('/search', [FaqController::class, 'search']);
    Route::get('/by-category/{categoryId}', [FaqController::class, 'getByCategory']);
    Route::get('/{id}', [FaqController::class, 'show']);
    Route::post('/', [FaqController::class, 'store']);
    Route::put('/{id}', [FaqController::class, 'update']);
    Route::delete('/{id}', [FaqController::class, 'destroy']);
    Route::patch('/{id}/toggle-active', [FaqController::class, 'toggleActive']);
    Route::patch('/{id}/toggle-featured', [FaqController::class, 'toggleFeatured']);
    Route::post('/reorder', [FaqController::class, 'reorder']);
    Route::post('/bulk-update', [FaqController::class, 'bulkUpdate']);
});

// Contact Messages API Routes
Route::prefix('contact-messages')->group(function () {
    Route::get('/', [ContactMessageController::class, 'index']);
    Route::get('/stats', [ContactMessageController::class, 'getStats']);
    Route::get('/export', [ContactMessageController::class, 'export']);
    Route::get('/{id}', [ContactMessageController::class, 'show']);
    Route::post('/', [ContactMessageController::class, 'store']);
    Route::put('/{id}', [ContactMessageController::class, 'update']);
    Route::delete('/{id}', [ContactMessageController::class, 'destroy']);
    Route::patch('/{id}/status', [ContactMessageController::class, 'updateStatus']);
    Route::post('/bulk-update', [ContactMessageController::class, 'bulkUpdate']);
});

// Testimonials API Routes
Route::prefix('testimonials')->group(function () {
    Route::get('/', [TestimonialController::class, 'index']);
    Route::get('/public', [TestimonialController::class, 'getPublic']);
    Route::get('/featured', [TestimonialController::class, 'getFeatured']);
    Route::get('/stats', [TestimonialController::class, 'getStats']);
    Route::get('/by-service/{serviceId}', [TestimonialController::class, 'getByService']);
    Route::get('/{id}', [TestimonialController::class, 'show']);
    Route::post('/', [TestimonialController::class, 'store']);
    Route::put('/{id}', [TestimonialController::class, 'update']);
    Route::delete('/{id}', [TestimonialController::class, 'destroy']);
    Route::patch('/{id}/toggle-approved', [TestimonialController::class, 'toggleApproved']);
    Route::patch('/{id}/toggle-featured', [TestimonialController::class, 'toggleFeatured']);
    Route::post('/reorder', [TestimonialController::class, 'reorder']);
    Route::post('/bulk-update', [TestimonialController::class, 'bulkUpdate']);
});

// Example protected route
Route::middleware('auth:api')->get('/protected', function () {
    return response()->json([
        'message' => 'This is a protected route',
        'user' => auth()->user()
    ]);
});