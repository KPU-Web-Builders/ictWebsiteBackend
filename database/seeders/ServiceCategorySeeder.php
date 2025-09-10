<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'description' => 'Custom web development services including frontend and backend development',
                'icon' => 'fas fa-code',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'Mobile Development',
                'slug' => 'mobile-development',
                'description' => 'iOS and Android mobile application development',
                'icon' => 'fas fa-mobile-alt',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'Digital Marketing',
                'slug' => 'digital-marketing',
                'description' => 'SEO, social media marketing, and online advertising services',
                'icon' => 'fas fa-bullhorn',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
            ],
            [
                'name' => 'UI/UX Design',
                'slug' => 'ui-ux-design',
                'description' => 'User interface and user experience design services',
                'icon' => 'fas fa-paint-brush',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            ServiceCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}