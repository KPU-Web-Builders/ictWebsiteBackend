<?php

namespace Database\Seeders;

use App\Models\TypeOfHosting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeOfHostingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hostingTypes = [
            [
                'name' => 'Shared Hosting',
                'description' => 'Affordable hosting solution where multiple websites share the same server resources. Perfect for small businesses, blogs, and personal websites with moderate traffic.',
                'image' => null,
            ],
            [
                'name' => 'VPS Hosting',
                'description' => 'Virtual Private Server hosting provides dedicated resources within a shared environment. Ideal for growing businesses that need more control and better performance than shared hosting.',
                'image' => null,
            ],
            [
                'name' => 'Dedicated Hosting',
                'description' => 'Exclusive server dedicated entirely to your website. Offers maximum performance, security, and control. Best for high-traffic websites and large enterprises.',
                'image' => null,
            ],
            [
                'name' => 'Cloud Hosting',
                'description' => 'Scalable hosting solution that uses multiple servers to balance load and maximize uptime. Resources can be scaled up or down based on demand, ensuring optimal performance.',
                'image' => null,
            ],
            [
                'name' => 'WordPress Hosting',
                'description' => 'Optimized hosting specifically designed for WordPress websites. Includes automatic updates, enhanced security, and performance optimizations tailored for WordPress.',
                'image' => null,
            ],
        ];

        foreach ($hostingTypes as $type) {
            TypeOfHosting::create($type);
        }
    }
}
