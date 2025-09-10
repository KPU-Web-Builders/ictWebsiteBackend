<?php

namespace Database\Seeders;

use App\Models\PlanFeature;
use App\Models\HostingPlan;
use Illuminate\Database\Seeder;

class PlanFeatureSeeder extends Seeder
{
    public function run(): void
    {
        // Get hosting plans
        $starterPlan = HostingPlan::where('slug', 'starter-plan')->first();
        $professionalPlan = HostingPlan::where('slug', 'professional-plan')->first();
        $enterprisePlan = HostingPlan::where('slug', 'enterprise-plan')->first();
        $customPlan = HostingPlan::where('slug', 'custom-plan')->first();

        $features = [
            // Starter Plan Features
            [
                'plan_id' => $starterPlan?->id,
                'feature_name' => 'Storage Space',
                'feature_value' => '10 GB',
                'is_included' => true,
                'sort_order' => 1,
            ],
            [
                'plan_id' => $starterPlan?->id,
                'feature_name' => 'Monthly Bandwidth',
                'feature_value' => '100 GB',
                'is_included' => true,
                'sort_order' => 2,
            ],
            [
                'plan_id' => $starterPlan?->id,
                'feature_name' => 'Email Accounts',
                'feature_value' => '5',
                'is_included' => true,
                'sort_order' => 3,
            ],
            [
                'plan_id' => $starterPlan?->id,
                'feature_name' => 'SSL Certificate',
                'feature_value' => 'Free',
                'is_included' => true,
                'sort_order' => 4,
            ],
            [
                'plan_id' => $starterPlan?->id,
                'feature_name' => '24/7 Support',
                'feature_value' => 'Basic',
                'is_included' => true,
                'sort_order' => 5,
            ],

            // Professional Plan Features
            [
                'plan_id' => $professionalPlan?->id,
                'feature_name' => 'Storage Space',
                'feature_value' => '50 GB',
                'is_included' => true,
                'sort_order' => 1,
            ],
            [
                'plan_id' => $professionalPlan?->id,
                'feature_name' => 'Monthly Bandwidth',
                'feature_value' => '500 GB',
                'is_included' => true,
                'sort_order' => 2,
            ],
            [
                'plan_id' => $professionalPlan?->id,
                'feature_name' => 'Email Accounts',
                'feature_value' => '25',
                'is_included' => true,
                'sort_order' => 3,
            ],
            [
                'plan_id' => $professionalPlan?->id,
                'feature_name' => 'SSL Certificate',
                'feature_value' => 'Free',
                'is_included' => true,
                'sort_order' => 4,
            ],
            [
                'plan_id' => $professionalPlan?->id,
                'feature_name' => '24/7 Support',
                'feature_value' => 'Priority',
                'is_included' => true,
                'sort_order' => 5,
            ],
            [
                'plan_id' => $professionalPlan?->id,
                'feature_name' => 'Daily Backups',
                'feature_value' => 'Included',
                'is_included' => true,
                'sort_order' => 6,
            ],

            // Enterprise Plan Features
            [
                'plan_id' => $enterprisePlan?->id,
                'feature_name' => 'Storage Space',
                'feature_value' => '200 GB',
                'is_included' => true,
                'sort_order' => 1,
            ],
            [
                'plan_id' => $enterprisePlan?->id,
                'feature_name' => 'Monthly Bandwidth',
                'feature_value' => 'Unlimited',
                'is_included' => true,
                'sort_order' => 2,
            ],
            [
                'plan_id' => $enterprisePlan?->id,
                'feature_name' => 'Email Accounts',
                'feature_value' => 'Unlimited',
                'is_included' => true,
                'sort_order' => 3,
            ],
            [
                'plan_id' => $enterprisePlan?->id,
                'feature_name' => 'SSL Certificate',
                'feature_value' => 'Premium',
                'is_included' => true,
                'sort_order' => 4,
            ],
            [
                'plan_id' => $enterprisePlan?->id,
                'feature_name' => '24/7 Support',
                'feature_value' => 'Premium',
                'is_included' => true,
                'sort_order' => 5,
            ],
            [
                'plan_id' => $enterprisePlan?->id,
                'feature_name' => 'Daily Backups',
                'feature_value' => 'Included',
                'is_included' => true,
                'sort_order' => 6,
            ],
            [
                'plan_id' => $enterprisePlan?->id,
                'feature_name' => 'CDN Integration',
                'feature_value' => 'Included',
                'is_included' => true,
                'sort_order' => 7,
            ],

            // Custom Plan Features
            [
                'plan_id' => $customPlan?->id,
                'feature_name' => 'Storage Space',
                'feature_value' => 'Custom',
                'is_included' => true,
                'sort_order' => 1,
            ],
            [
                'plan_id' => $customPlan?->id,
                'feature_name' => 'Monthly Bandwidth',
                'feature_value' => 'Custom',
                'is_included' => true,
                'sort_order' => 2,
            ],
            [
                'plan_id' => $customPlan?->id,
                'feature_name' => 'Email Accounts',
                'feature_value' => 'Custom',
                'is_included' => true,
                'sort_order' => 3,
            ],
            [
                'plan_id' => $customPlan?->id,
                'feature_name' => 'Dedicated Support Manager',
                'feature_value' => 'Included',
                'is_included' => true,
                'sort_order' => 4,
            ],
            [
                'plan_id' => $customPlan?->id,
                'feature_name' => 'Custom Integrations',
                'feature_value' => 'Available',
                'is_included' => true,
                'sort_order' => 5,
            ],
        ];

        foreach ($features as $feature) {
            PlanFeature::create($feature);
        }
    }
}