<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Get category IDs for foreign keys
        $webDevCategory = ServiceCategory::where('slug', 'web-development')->first();
        $mobileDevCategory = ServiceCategory::where('slug', 'mobile-development')->first();
        $digitalMarketingCategory = ServiceCategory::where('slug', 'digital-marketing')->first();
        $uiUxCategory = ServiceCategory::where('slug', 'ui-ux-design')->first();

        $services = [
            // Web Development Services
            [
                'name' => 'Custom Website Development',
                'slug' => 'custom-website-development',
                'description' => 'Build custom websites tailored to your business needs using modern technologies',
                'category_id' => $webDevCategory?->id,
                'icon' => 'fas fa-globe',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'E-commerce Development',
                'slug' => 'ecommerce-development',
                'description' => 'Complete e-commerce solutions with payment integration and inventory management',
                'category_id' => $webDevCategory?->id,
                'icon' => 'fas fa-shopping-cart',
                'sort_order' => 2,
                'is_active' => true,
            ],
            
            // Mobile Development Services
            [
                'name' => 'iOS App Development',
                'slug' => 'ios-app-development',
                'description' => 'Native iOS applications for iPhone and iPad using Swift and Objective-C',
                'category_id' => $mobileDevCategory?->id,
                'icon' => 'fab fa-apple',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Android App Development',
                'slug' => 'android-app-development',
                'description' => 'Native Android applications using Kotlin and Java',
                'category_id' => $mobileDevCategory?->id,
                'icon' => 'fab fa-android',
                'sort_order' => 2,
                'is_active' => true,
            ],
            
            // Digital Marketing Services
            [
                'name' => 'SEO Optimization',
                'slug' => 'seo-optimization',
                'description' => 'Search engine optimization to improve your website ranking',
                'category_id' => $digitalMarketingCategory?->id,
                'icon' => 'fas fa-search',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Social Media Marketing',
                'slug' => 'social-media-marketing',
                'description' => 'Comprehensive social media strategy and content management',
                'category_id' => $digitalMarketingCategory?->id,
                'icon' => 'fas fa-hashtag',
                'sort_order' => 2,
                'is_active' => true,
            ],
            
            // UI/UX Design Services
            [
                'name' => 'Website Design',
                'slug' => 'website-design',
                'description' => 'Modern and responsive website designs that convert visitors to customers',
                'category_id' => $uiUxCategory?->id,
                'icon' => 'fas fa-desktop',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Mobile App Design',
                'slug' => 'mobile-app-design',
                'description' => 'User-friendly mobile app interfaces with excellent user experience',
                'category_id' => $uiUxCategory?->id,
                'icon' => 'fas fa-mobile',
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }
    }
}