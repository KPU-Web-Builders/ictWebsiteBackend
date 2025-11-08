<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\FaqCategoryController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\HostingPlanController;
use App\Http\Controllers\PlanFeatureController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SiteSettingController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ServicesCardController;
use App\Http\Controllers\TypeOfHostingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
//jwt 
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication Routes
Route::group(['prefix' => 'auth'], function () {
    // Public routes (no authentication required)
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);

    // Protected routes (require authentication)
    Route::middleware('auth.jwt')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
    });
});

// Site Settings API Routes
Route::prefix('settings')->group(function () {
    // Public routes
    Route::get('/', [SiteSettingController::class, 'index']);
    Route::get('/{key}', [SiteSettingController::class, 'show']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [SiteSettingController::class, 'store']);
        Route::put('/{key}', [SiteSettingController::class, 'update']);
        Route::delete('/{key}', [SiteSettingController::class, 'destroy']);
    });
});

// Service Categories API Routes
Route::prefix('service-categories')->group(function () {
    // Public routes
    Route::get('/', [ServiceCategoryController::class, 'index']);
    Route::get('/{id}', [ServiceCategoryController::class, 'show']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [ServiceCategoryController::class, 'store']);
        Route::put('/{id}', [ServiceCategoryController::class, 'update']);
        Route::delete('/{id}', [ServiceCategoryController::class, 'destroy']);
        Route::patch('/{id}/toggle-active', [ServiceCategoryController::class, 'toggleActive']);
    });
});

// Services API Routes
Route::prefix('services')->group(function () {
    // Public routes
    Route::get('/', [ServiceController::class, 'index']);
    Route::get('/{id}', [ServiceController::class, 'show']);
    Route::get('/by-category/{categoryId}', [ServiceController::class, 'getByCategory']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [ServiceController::class, 'store']);
        Route::put('/{id}', [ServiceController::class, 'update']);
        Route::delete('/{id}', [ServiceController::class, 'destroy']);
        Route::patch('/{id}/toggle-active', [ServiceController::class, 'toggleActive']);
    });
});

// Hosting Plans API Routes
Route::prefix('hosting-plans')->group(function () {
    // Public routes
    Route::get('/', [HostingPlanController::class, 'index']);
    Route::get('/{id}', [HostingPlanController::class, 'show']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [HostingPlanController::class, 'store']);
        Route::put('/{id}', [HostingPlanController::class, 'update']);
        Route::delete('/{id}', [HostingPlanController::class, 'destroy']);
        Route::patch('/{id}/toggle-active', [HostingPlanController::class, 'toggleActive']);
        Route::patch('/{id}/toggle-highlighted', [HostingPlanController::class, 'toggleHighlighted']);
        Route::patch('/{id}/toggle-popular', [HostingPlanController::class, 'togglePopular']);
    });
});

// Plan Features API Routes
Route::prefix('plan-features')->group(function () {
    // Public routes
    Route::get('/', [PlanFeatureController::class, 'index']);
    Route::get('/{id}', [PlanFeatureController::class, 'show']);
    Route::get('/by-plan/{planId}', [PlanFeatureController::class, 'getByPlan']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [PlanFeatureController::class, 'store']);
        Route::post('/bulk', [PlanFeatureController::class, 'bulkStore']);
        Route::put('/{id}', [PlanFeatureController::class, 'update']);
        Route::delete('/{id}', [PlanFeatureController::class, 'destroy']);
        Route::patch('/{id}/toggle-included', [PlanFeatureController::class, 'toggleIncluded']);
    });
});

// Portfolio API Routes
Route::prefix('portfolio')->group(function () {
    // Public routes
    Route::get('/', [PortfolioController::class, 'index']);
    Route::get('/{slug}', [PortfolioController::class, 'show']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [PortfolioController::class, 'store']);
        Route::put('/{slug}', [PortfolioController::class, 'update']);
        Route::delete('/{slug}', [PortfolioController::class, 'destroy']);
        Route::patch('/{slug}/toggle-featured', [PortfolioController::class, 'toggleFeatured']);
        Route::patch('/{slug}/toggle-published', [PortfolioController::class, 'togglePublished']);
    });
});

// Team Members API Routes
Route::prefix('team-members')->group(function () {
    // Public routes
    Route::get('/', [TeamMemberController::class, 'index']);
    Route::get('/{id}', [TeamMemberController::class, 'show']);
    Route::get('/by-role/{role}', [TeamMemberController::class, 'getByRole']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [TeamMemberController::class, 'store']);
        Route::put('/{id}', [TeamMemberController::class, 'update']);
        Route::delete('/{id}', [TeamMemberController::class, 'destroy']);
        Route::patch('/{id}/toggle-active', [TeamMemberController::class, 'toggleActive']);
        Route::patch('/{id}/toggle-verified', [TeamMemberController::class, 'toggleVerified']);
    });
});

// FAQ Categories API Routes
Route::prefix('faq-categories')->group(function () {
    // Public routes
    Route::get('/', [FaqCategoryController::class, 'index']);
    Route::get('/active-with-faqs', [FaqCategoryController::class, 'getActiveWithFaqs']);
    Route::get('/{id}', [FaqCategoryController::class, 'show']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [FaqCategoryController::class, 'store']);
        Route::put('/{id}', [FaqCategoryController::class, 'update']);
        Route::delete('/{id}', [FaqCategoryController::class, 'destroy']);
        Route::patch('/{id}/toggle-active', [FaqCategoryController::class, 'toggleActive']);
        Route::post('/reorder', [FaqCategoryController::class, 'reorder']);
    });
});

// FAQs API Routes
Route::prefix('faqs')->group(function () {
    // Public routes
    Route::get('/', [FaqController::class, 'index']);
    Route::get('/featured', [FaqController::class, 'getFeatured']);
    Route::get('/public', [FaqController::class, 'getPublic']);
    Route::get('/search', [FaqController::class, 'search']);
    Route::get('/by-category/{categoryId}', [FaqController::class, 'getByCategory']);
    Route::get('/{id}', [FaqController::class, 'show']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [FaqController::class, 'store']);
        Route::put('/{id}', [FaqController::class, 'update']);
        Route::delete('/{id}', [FaqController::class, 'destroy']);
        Route::patch('/{id}/toggle-active', [FaqController::class, 'toggleActive']);
        Route::patch('/{id}/toggle-featured', [FaqController::class, 'toggleFeatured']);
        Route::post('/reorder', [FaqController::class, 'reorder']);
        Route::post('/bulk-update', [FaqController::class, 'bulkUpdate']);
    });
});

// Contact Messages API Routes
Route::prefix('contact-messages')->group(function () {
    // Public route - anyone can submit a contact message
    Route::post('/', [ContactMessageController::class, 'store']);

    // Protected routes - only authenticated users can view/manage messages
    Route::middleware('auth.jwt')->group(function () {
        Route::get('/', [ContactMessageController::class, 'index']);
        Route::get('/stats', [ContactMessageController::class, 'getStats']);
        Route::get('/export', [ContactMessageController::class, 'export']);
        Route::get('/{id}', [ContactMessageController::class, 'show']);
        Route::put('/{id}', [ContactMessageController::class, 'update']);
        Route::delete('/{id}', [ContactMessageController::class, 'destroy']);
        Route::patch('/{id}/status', [ContactMessageController::class, 'updateStatus']);
        Route::post('/bulk-update', [ContactMessageController::class, 'bulkUpdate']);
    });
});

// Testimonials API Routes
Route::prefix('testimonials')->group(function () {
    // Public routes
    Route::get('/public', [TestimonialController::class, 'getPublic']);
    Route::get('/featured', [TestimonialController::class, 'getFeatured']);
    Route::get('/by-service/{serviceId}', [TestimonialController::class, 'getByService']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::get('/', [TestimonialController::class, 'index']);
        Route::get('/stats', [TestimonialController::class, 'getStats']);
        Route::get('/{id}', [TestimonialController::class, 'show']);
        Route::post('/', [TestimonialController::class, 'store']);
        Route::put('/{id}', [TestimonialController::class, 'update']);
        Route::delete('/{id}', [TestimonialController::class, 'destroy']);
        Route::patch('/{id}/toggle-approved', [TestimonialController::class, 'toggleApproved']);
        Route::patch('/{id}/toggle-featured', [TestimonialController::class, 'toggleFeatured']);
        Route::post('/reorder', [TestimonialController::class, 'reorder']);
        Route::post('/bulk-update', [TestimonialController::class, 'bulkUpdate']);
    });
});

// Partners API Routes
Route::prefix('partners')->group(function () {
    // Public routes
    Route::get('/', [PartnersController::class, 'index']);
    Route::get('/{id}', [PartnersController::class, 'show']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [PartnersController::class, 'store']);
        Route::put('/{id}', [PartnersController::class, 'update']);
        Route::delete('/{id}', [PartnersController::class, 'destroy']);
    });
});

// Services Cards API Routes
Route::prefix('services-cards')->group(function () {
    // Public routes
    Route::get('/', [ServicesCardController::class, 'index']);
    Route::get('/{id}', [ServicesCardController::class, 'show']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [ServicesCardController::class, 'store']);
        Route::put('/{id}', [ServicesCardController::class, 'update']);
        Route::delete('/{id}', [ServicesCardController::class, 'destroy']);
    });
});

// Type of Hosting API Routes
Route::prefix('type-of-hostings')->group(function () {
    // Public routes
    Route::get('/', [TypeOfHostingController::class, 'index']);
    Route::get('/{id}', [TypeOfHostingController::class, 'show']);

    // Protected routes
    Route::middleware('auth.jwt')->group(function () {
        Route::post('/', [TypeOfHostingController::class, 'store']);
        Route::put('/{id}', [TypeOfHostingController::class, 'update']);
        Route::delete('/{id}', [TypeOfHostingController::class, 'destroy']);
    });
});

// Example protected route
Route::middleware('auth.jwt')->get('/protected', function () {
    return response()->json([
        'message' => 'This is a protected route',
        'user' => auth('api')->user()
    ]);
});
