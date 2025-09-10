<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        // Get category IDs
        $generalCategory = FaqCategory::where('name', 'General Questions')->first();
        $webDevCategory = FaqCategory::where('name', 'Web Development')->first();
        $mobileCategory = FaqCategory::where('name', 'Mobile Apps')->first();
        $hostingCategory = FaqCategory::where('name', 'Hosting & Maintenance')->first();
        $marketingCategory = FaqCategory::where('name', 'Digital Marketing')->first();
        $pricingCategory = FaqCategory::where('name', 'Pricing & Billing')->first();

        $faqs = [
            // General Questions
            [
                'category_id' => $generalCategory?->id,
                'question' => 'What services does ICT Solutions provide?',
                'answer' => 'We provide comprehensive ICT services including web development, mobile app development, digital marketing, UI/UX design, hosting solutions, and ongoing maintenance and support.',
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $generalCategory?->id,
                'question' => 'How long have you been in business?',
                'answer' => 'ICT Solutions has been providing professional technology services for over 8 years, serving clients ranging from startups to enterprise-level organizations.',
                'is_featured' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category_id' => $generalCategory?->id,
                'question' => 'Do you work with clients internationally?',
                'answer' => 'Yes, we work with clients globally. Our team is experienced in working across different time zones and can accommodate various communication preferences.',
                'is_featured' => false,
                'sort_order' => 3,
                'is_active' => true,
            ],

            // Web Development
            [
                'category_id' => $webDevCategory?->id,
                'question' => 'What technologies do you use for web development?',
                'answer' => 'We use modern technologies including Laravel, React, Vue.js, Node.js, Python, and various database systems. We choose the best technology stack based on your project requirements.',
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $webDevCategory?->id,
                'question' => 'How long does it take to develop a website?',
                'answer' => 'Development time varies based on complexity. A simple website takes 2-4 weeks, while complex web applications can take 2-6 months. We provide detailed timelines during project planning.',
                'is_featured' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'category_id' => $webDevCategory?->id,
                'question' => 'Do you provide website maintenance after launch?',
                'answer' => 'Yes, we offer comprehensive maintenance packages including security updates, performance optimization, content updates, and technical support.',
                'is_featured' => false,
                'sort_order' => 3,
                'is_active' => true,
            ],

            // Mobile Apps
            [
                'category_id' => $mobileCategory?->id,
                'question' => 'Do you develop for both iOS and Android?',
                'answer' => 'Yes, we develop native apps for both iOS and Android platforms. We also offer cross-platform development using React Native and Flutter for cost-effective solutions.',
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $mobileCategory?->id,
                'question' => 'What is the typical cost of mobile app development?',
                'answer' => 'App development costs vary based on features and complexity. Simple apps start from $10,000, while complex enterprise apps can range from $50,000-$200,000+. We provide detailed quotes after requirement analysis.',
                'is_featured' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],

            // Hosting & Maintenance
            [
                'category_id' => $hostingCategory?->id,
                'question' => 'What hosting plans do you offer?',
                'answer' => 'We offer various hosting plans from starter packages for small websites to enterprise solutions for high-traffic applications. All plans include SSL certificates, regular backups, and 24/7 support.',
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $hostingCategory?->id,
                'question' => 'Do you provide website backups?',
                'answer' => 'Yes, all our hosting plans include automated daily backups with easy restoration options. We also maintain off-site backup copies for additional security.',
                'is_featured' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],

            // Digital Marketing
            [
                'category_id' => $marketingCategory?->id,
                'question' => 'What digital marketing services do you provide?',
                'answer' => 'We offer SEO optimization, social media marketing, Google Ads management, content marketing, email marketing, and comprehensive digital marketing strategies.',
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $marketingCategory?->id,
                'question' => 'How long does it take to see SEO results?',
                'answer' => 'SEO is a long-term strategy. You may see initial improvements in 2-3 months, with significant results typically visible after 6-12 months of consistent optimization efforts.',
                'is_featured' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],

            // Pricing & Billing
            [
                'category_id' => $pricingCategory?->id,
                'question' => 'What payment methods do you accept?',
                'answer' => 'We accept various payment methods including bank transfers, credit cards, PayPal, and cryptocurrency payments. Payment terms are flexible and discussed during project negotiation.',
                'is_featured' => true,
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'category_id' => $pricingCategory?->id,
                'question' => 'Do you offer payment plans for large projects?',
                'answer' => 'Yes, for larger projects we offer milestone-based payment plans. Typically, we require 30% upfront, with remaining payments tied to project milestones and final delivery.',
                'is_featured' => false,
                'sort_order' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::create($faq);
        }
    }
}