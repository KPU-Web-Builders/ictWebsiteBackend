<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ServiceCategorySeeder::class,
            ServiceSeeder::class,
            SiteSettingSeeder::class,
            HostingPlanSeeder::class,
            PlanFeatureSeeder::class,
            FaqCategorySeeder::class,
            FaqSeeder::class,
            TeamMemberSeeder::class,
            PortfolioSeeder::class,
            TestimonialSeeder::class,
            ContactMessageSeeder::class,
        ]);
    }
}
