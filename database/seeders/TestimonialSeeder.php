<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use App\Models\Service;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        // Get service IDs
        $webDevService = Service::where('slug', 'custom-website-development')->first();
        $ecommerceService = Service::where('slug', 'ecommerce-development')->first();
        $iosService = Service::where('slug', 'ios-app-development')->first();
        $seoService = Service::where('slug', 'seo-optimization')->first();
        $designService = Service::where('slug', 'website-design')->first();

        $testimonials = [
            [
                'client_name' => 'Robert Johnson',
                'company' => 'TechStart Solutions Inc.',
                'position' => 'CEO',
                'testimonial' => 'ICT Solutions delivered exactly what we needed for our company website. Their team was professional, responsive, and delivered the project on time and within budget. The website has significantly improved our online presence and lead generation.',
                'rating' => 5,
                'photo_url' => '/images/testimonials/robert-johnson.jpg',
                'service_id' => $webDevService?->id,
                'is_featured' => true,
                'is_approved' => true,
                'sort_order' => 1,
                'created_at' => now(),
            ],
            [
                'client_name' => 'Maria Garcia',
                'company' => 'ShopNow Retail',
                'position' => 'Marketing Director',
                'testimonial' => 'The e-commerce platform they built for us has transformed our business. Sales have increased by 300% since launch, and the admin dashboard makes managing our inventory so much easier. Highly recommend their services!',
                'rating' => 5,
                'photo_url' => '/images/testimonials/maria-garcia.jpg',
                'service_id' => $ecommerceService?->id,
                'is_featured' => true,
                'is_approved' => true,
                'sort_order' => 2,
                'created_at' => now(),
            ],
            [
                'client_name' => 'David Chen',
                'company' => 'FitLife Technologies',
                'position' => 'Founder',
                'testimonial' => 'Our iOS app development experience with ICT Solutions was outstanding. They understood our vision and created a beautiful, functional app that our users love. The app has received excellent reviews on the App Store.',
                'rating' => 5,
                'photo_url' => '/images/testimonials/david-chen.jpg',
                'service_id' => $iosService?->id,
                'is_featured' => true,
                'is_approved' => true,
                'sort_order' => 3,
                'created_at' => now(),
            ],
            [
                'client_name' => 'Sarah Williams',
                'company' => 'Local Business Solutions',
                'position' => 'Owner',
                'testimonial' => 'The SEO work they did for our website has been incredible. We went from page 3 to the first page of Google for our main keywords within 6 months. Our organic traffic has increased by 250%.',
                'rating' => 5,
                'photo_url' => '/images/testimonials/sarah-williams.jpg',
                'service_id' => $seoService?->id,
                'is_featured' => false,
                'is_approved' => true,
                'sort_order' => 4,
                'created_at' => now(),
            ],
            [
                'client_name' => 'Michael Thompson',
                'company' => 'Contemporary Art Gallery',
                'position' => 'Gallery Manager',
                'testimonial' => 'The website design perfectly captures the essence of our gallery. It\'s elegant, user-friendly, and showcases our artworks beautifully. We\'ve received numerous compliments from visitors and artists alike.',
                'rating' => 5,
                'photo_url' => '/images/testimonials/michael-thompson.jpg',
                'service_id' => $designService?->id,
                'is_featured' => false,
                'is_approved' => true,
                'sort_order' => 5,
                'created_at' => now(),
            ],
            [
                'client_name' => 'Jennifer Lee',
                'company' => 'GreenTech Innovations',
                'position' => 'CTO',
                'testimonial' => 'Working with ICT Solutions was a pleasure from start to finish. Their technical expertise and attention to detail ensured our project was completed flawlessly. We continue to work with them for ongoing maintenance and updates.',
                'rating' => 4,
                'photo_url' => '/images/testimonials/jennifer-lee.jpg',
                'service_id' => $webDevService?->id,
                'is_featured' => false,
                'is_approved' => true,
                'sort_order' => 6,
                'created_at' => now(),
            ],
            [
                'client_name' => 'Alex Rodriguez',
                'company' => 'StartupXYZ',
                'position' => 'Co-founder',
                'testimonial' => 'ICT Solutions helped us bring our startup idea to life with a modern, scalable web application. Their team\'s creativity and technical skills exceeded our expectations. The project was delivered on schedule and the results speak for themselves.',
                'rating' => 5,
                'photo_url' => '/images/testimonials/alex-rodriguez.jpg',
                'service_id' => $webDevService?->id,
                'is_featured' => false,
                'is_approved' => true,
                'sort_order' => 7,
                'created_at' => now(),
            ],
            [
                'client_name' => 'Lisa Park',
                'company' => 'Healthcare Plus',
                'position' => 'Operations Manager',
                'testimonial' => 'The team at ICT Solutions understood our specific healthcare industry requirements and delivered a HIPAA-compliant solution that works perfectly for our needs. Professional, reliable, and highly recommended.',
                'rating' => 4,
                'photo_url' => '/images/testimonials/lisa-park.jpg',
                'service_id' => $webDevService?->id,
                'is_featured' => false,
                'is_approved' => true,
                'sort_order' => 8,
                'created_at' => now(),
            ],
            [
                'client_name' => 'James Wilson',
                'company' => 'Wilson Consulting',
                'position' => 'Principal Consultant',
                'testimonial' => 'Outstanding service and results! ICT Solutions transformed our outdated website into a modern, professional platform that truly represents our business. The increase in client inquiries has been remarkable.',
                'rating' => 5,
                'photo_url' => '/images/testimonials/james-wilson.jpg',
                'service_id' => $designService?->id,
                'is_featured' => false,
                'is_approved' => true,
                'sort_order' => 9,
                'created_at' => now(),
            ],
            [
                'client_name' => 'Amanda Foster',
                'company' => 'Digital Marketing Pro',
                'position' => 'Agency Owner',
                'testimonial' => 'As a marketing agency, we have high standards for web development partners. ICT Solutions consistently delivers high-quality work for our clients. They\'re reliable, skilled, and easy to work with.',
                'rating' => 4,
                'photo_url' => '/images/testimonials/amanda-foster.jpg',
                'service_id' => $webDevService?->id,
                'is_featured' => false,
                'is_approved' => true,
                'sort_order' => 10,
                'created_at' => now(),
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::create($testimonial);
        }
    }
}