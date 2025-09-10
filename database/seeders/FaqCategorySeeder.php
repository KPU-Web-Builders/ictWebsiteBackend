<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

class FaqCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'General Questions',
                'description' => 'Common questions about our services and company',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Web Development',
                'description' => 'Frequently asked questions about web development services',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Mobile Apps',
                'description' => 'Questions about mobile application development',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Hosting & Maintenance',
                'description' => 'Questions about hosting plans and website maintenance',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Digital Marketing',
                'description' => 'SEO, social media, and digital marketing questions',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Pricing & Billing',
                'description' => 'Questions about pricing, billing, and payment methods',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            FaqCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}