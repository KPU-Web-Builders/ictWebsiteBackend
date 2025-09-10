<?php

namespace Database\Seeders;

use App\Models\HostingPlan;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class HostingPlanSeeder extends Seeder
{
    public function run(): void
    {
        // Get a web development category for hosting plans
        $webCategory = ServiceCategory::where('slug', 'web-development')->first();

        $plans = [
            [
                'name' => 'Starter Plan',
                'slug' => 'starter-plan',
                'description' => 'Perfect for small websites and personal projects',
                'logo_url' => '/images/plans/starter.png',
                'category_id' => $webCategory?->id,
                'monthly_price' => 9.99,
                'yearly_price' => 99.99,
                'monthly_renewal_price' => 12.99,
                'yearly_renewal_price' => 129.99,
                'is_highlighted' => false,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional Plan',
                'slug' => 'professional-plan',
                'description' => 'Ideal for growing businesses and medium-sized websites',
                'logo_url' => '/images/plans/professional.png',
                'category_id' => $webCategory?->id,
                'monthly_price' => 19.99,
                'yearly_price' => 199.99,
                'monthly_renewal_price' => 24.99,
                'yearly_renewal_price' => 249.99,
                'is_highlighted' => true,
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise Plan',
                'slug' => 'enterprise-plan',
                'description' => 'Advanced solution for large enterprises and high-traffic sites',
                'logo_url' => '/images/plans/enterprise.png',
                'category_id' => $webCategory?->id,
                'monthly_price' => 49.99,
                'yearly_price' => 499.99,
                'monthly_renewal_price' => 59.99,
                'yearly_renewal_price' => 599.99,
                'is_highlighted' => false,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Custom Plan',
                'slug' => 'custom-plan',
                'description' => 'Tailored solution for specific business requirements',
                'logo_url' => '/images/plans/custom.png',
                'category_id' => $webCategory?->id,
                'monthly_price' => 99.99,
                'yearly_price' => 999.99,
                'monthly_renewal_price' => 119.99,
                'yearly_renewal_price' => 1199.99,
                'is_highlighted' => false,
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($plans as $plan) {
            HostingPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}