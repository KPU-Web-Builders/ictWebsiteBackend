<?php

namespace Database\Seeders;

use App\Models\Portfolio;
use App\Models\Service;
use Illuminate\Database\Seeder;

class PortfolioSeeder extends Seeder
{
    public function run(): void
    {
        // Get service IDs
        $webDevService = Service::where('slug', 'custom-website-development')->first();
        $ecommerceService = Service::where('slug', 'ecommerce-development')->first();
        $iosService = Service::where('slug', 'ios-app-development')->first();
        $androidService = Service::where('slug', 'android-app-development')->first();
        $designService = Service::where('slug', 'website-design')->first();

        $portfolioItems = [
            [
                'title' => 'TechStart Solutions Website',
                'slug' => 'techstart-solutions-website',
                'description' => 'A modern corporate website for a technology startup featuring responsive design, interactive elements, and comprehensive service showcases.',
                'service_id' => $webDevService?->id,
                'client_name' => 'TechStart Solutions Inc.',
                'project_url' => 'https://techstartsolutions.com',
                'featured_image' => '/images/portfolio/techstart-featured.jpg',
                'gallery_images' => json_encode([
                    '/images/portfolio/techstart-1.jpg',
                    '/images/portfolio/techstart-2.jpg',
                    '/images/portfolio/techstart-3.jpg',
                    '/images/portfolio/techstart-4.jpg'
                ]),
                'technologies_used' => json_encode(['Laravel', 'React', 'MySQL', 'TailwindCSS', 'AWS']),
                'project_date' => '2024-08-15',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'ShopNow E-commerce Platform',
                'slug' => 'shopnow-ecommerce-platform',
                'description' => 'A full-featured e-commerce platform with advanced inventory management, payment integration, and customer analytics.',
                'service_id' => $ecommerceService?->id,
                'client_name' => 'ShopNow Retail',
                'project_url' => 'https://shopnowstore.com',
                'featured_image' => '/images/portfolio/shopnow-featured.jpg',
                'gallery_images' => json_encode([
                    '/images/portfolio/shopnow-1.jpg',
                    '/images/portfolio/shopnow-2.jpg',
                    '/images/portfolio/shopnow-3.jpg',
                    '/images/portfolio/shopnow-4.jpg',
                    '/images/portfolio/shopnow-5.jpg'
                ]),
                'technologies_used' => json_encode(['Laravel', 'Vue.js', 'Stripe API', 'Redis', 'PostgreSQL']),
                'project_date' => '2024-06-30',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'FitTracker Mobile App',
                'slug' => 'fittracker-mobile-app',
                'description' => 'A comprehensive fitness tracking app for iOS featuring workout planning, progress tracking, and social features.',
                'service_id' => $iosService?->id,
                'client_name' => 'FitLife Technologies',
                'project_url' => 'https://apps.apple.com/app/fittracker',
                'featured_image' => '/images/portfolio/fittracker-featured.jpg',
                'gallery_images' => json_encode([
                    '/images/portfolio/fittracker-1.jpg',
                    '/images/portfolio/fittracker-2.jpg',
                    '/images/portfolio/fittracker-3.jpg'
                ]),
                'technologies_used' => json_encode(['Swift', 'UIKit', 'Core Data', 'HealthKit', 'Firebase']),
                'project_date' => '2024-09-01',
                'is_featured' => true,
                'is_published' => true,
            ],
            [
                'title' => 'FoodDelivery Android App',
                'slug' => 'fooddelivery-android-app',
                'description' => 'A food delivery application for Android with real-time tracking, payment integration, and restaurant management.',
                'service_id' => $androidService?->id,
                'client_name' => 'QuickEats Delivery',
                'project_url' => 'https://play.google.com/store/apps/details?id=com.quickeats',
                'featured_image' => '/images/portfolio/fooddelivery-featured.jpg',
                'gallery_images' => json_encode([
                    '/images/portfolio/fooddelivery-1.jpg',
                    '/images/portfolio/fooddelivery-2.jpg',
                    '/images/portfolio/fooddelivery-3.jpg',
                    '/images/portfolio/fooddelivery-4.jpg'
                ]),
                'technologies_used' => json_encode(['Kotlin', 'Android Jetpack', 'Google Maps API', 'Firebase', 'Stripe']),
                'project_date' => '2024-07-20',
                'is_featured' => false,
                'is_published' => true,
            ],
            [
                'title' => 'ArtGallery Portfolio Website',
                'slug' => 'artgallery-portfolio-website',
                'description' => 'An elegant portfolio website for an art gallery featuring responsive design and artwork showcase functionality.',
                'service_id' => $designService?->id,
                'client_name' => 'Contemporary Art Gallery',
                'project_url' => 'https://contemporaryartgallery.com',
                'featured_image' => '/images/portfolio/artgallery-featured.jpg',
                'gallery_images' => json_encode([
                    '/images/portfolio/artgallery-1.jpg',
                    '/images/portfolio/artgallery-2.jpg',
                    '/images/portfolio/artgallery-3.jpg'
                ]),
                'technologies_used' => json_encode(['HTML5', 'CSS3', 'JavaScript', 'GSAP', 'Webpack']),
                'project_date' => '2024-05-15',
                'is_featured' => false,
                'is_published' => true,
            ],
        ];

        foreach ($portfolioItems as $item) {
            Portfolio::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}