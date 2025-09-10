<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            [
                'setting_key' => 'site_name',
                'setting_value' => 'ICT Solutions',
                'setting_type' => 'text',
                'description' => 'Website name displayed in header and title',
            ],
            [
                'setting_key' => 'site_tagline',
                'setting_value' => 'Your Technology Partner for Digital Success',
                'setting_type' => 'text',
                'description' => 'Website tagline or slogan',
            ],
            [
                'setting_key' => 'site_logo',
                'setting_value' => '/images/logo.png',
                'setting_type' => 'image',
                'description' => 'Main website logo',
            ],
            [
                'setting_key' => 'contact_email',
                'setting_value' => 'info@ictsolutions.com',
                'setting_type' => 'text',
                'description' => 'Primary contact email address',
            ],
            [
                'setting_key' => 'contact_phone',
                'setting_value' => '+1 (555) 123-4567',
                'setting_type' => 'text',
                'description' => 'Primary contact phone number',
            ],
            [
                'setting_key' => 'office_address',
                'setting_value' => '123 Technology Street, Innovation City, IC 12345',
                'setting_type' => 'text',
                'description' => 'Office address for contact information',
            ],
            [
                'setting_key' => 'facebook_url',
                'setting_value' => 'https://facebook.com/ictsolutions',
                'setting_type' => 'text',
                'description' => 'Facebook page URL',
            ],
            [
                'setting_key' => 'twitter_url',
                'setting_value' => 'https://twitter.com/ictsolutions',
                'setting_type' => 'text',
                'description' => 'Twitter profile URL',
            ],
            [
                'setting_key' => 'linkedin_url',
                'setting_value' => 'https://linkedin.com/company/ictsolutions',
                'setting_type' => 'text',
                'description' => 'LinkedIn company page URL',
            ],
            [
                'setting_key' => 'enable_maintenance_mode',
                'setting_value' => 'false',
                'setting_type' => 'boolean',
                'description' => 'Enable/disable website maintenance mode',
            ],
            [
                'setting_key' => 'meta_description',
                'setting_value' => 'Professional ICT solutions including web development, mobile apps, and digital marketing services.',
                'setting_type' => 'text',
                'description' => 'Default meta description for SEO',
            ],
            [
                'setting_key' => 'meta_keywords',
                'setting_value' => 'ICT, web development, mobile apps, digital marketing, technology solutions',
                'setting_type' => 'text',
                'description' => 'Default meta keywords for SEO',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['setting_key' => $setting['setting_key']],
                $setting
            );
        }
    }
}