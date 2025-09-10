<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;

class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $teamMembers = [
            [
                'name' => 'John Smith',
                'role' => 'CEO & Founder',
                'bio' => 'John is the visionary behind ICT Solutions with over 15 years of experience in technology leadership. He founded the company with a mission to help businesses leverage technology for growth.',
                'email' => 'john.smith@ictsolutions.com',
                'phone' => '+1 (555) 123-4501',
                'photo_url' => '/images/team/john-smith.jpg',
                'linkedin_url' => 'https://linkedin.com/in/johnsmith',
                'twitter_url' => 'https://twitter.com/johnsmith',
                'skills' => json_encode(['Strategic Planning', 'Business Development', 'Technology Leadership']),
                'is_active' => true,
                'is_verified' => true,
                'sort_order' => 1,
                'joined_date' => '2015-01-01',
            ],
            [
                'name' => 'Sarah Johnson',
                'role' => 'Lead Developer',
                'bio' => 'Sarah leads our development team with expertise in full-stack development. She specializes in modern web technologies and has delivered over 100+ successful projects.',
                'email' => 'sarah.johnson@ictsolutions.com',
                'phone' => '+1 (555) 123-4502',
                'photo_url' => '/images/team/sarah-johnson.jpg',
                'linkedin_url' => 'https://linkedin.com/in/sarahjohnson',
                'github_url' => 'https://github.com/sarahjohnson',
                'skills' => json_encode(['Laravel', 'React', 'Vue.js', 'Node.js', 'Database Design']),
                'is_active' => true,
                'is_verified' => true,
                'sort_order' => 2,
                'joined_date' => '2017-03-15',
            ],
            [
                'name' => 'Michael Chen',
                'role' => 'Mobile App Developer',
                'bio' => 'Michael is our mobile development specialist with extensive experience in iOS and Android app development. He has published over 50 apps on both platforms.',
                'email' => 'michael.chen@ictsolutions.com',
                'phone' => '+1 (555) 123-4503',
                'photo_url' => '/images/team/michael-chen.jpg',
                'linkedin_url' => 'https://linkedin.com/in/michaelchen',
                'github_url' => 'https://github.com/michaelchen',
                'skills' => json_encode(['iOS Development', 'Android Development', 'React Native', 'Flutter']),
                'is_active' => true,
                'is_verified' => true,
                'sort_order' => 3,
                'joined_date' => '2019-07-01',
            ],
            [
                'name' => 'Emily Rodriguez',
                'role' => 'UI/UX Designer',
                'bio' => 'Emily creates stunning user interfaces and experiences that drive user engagement. Her design philosophy focuses on simplicity, functionality, and aesthetic appeal.',
                'email' => 'emily.rodriguez@ictsolutions.com',
                'phone' => '+1 (555) 123-4504',
                'photo_url' => '/images/team/emily-rodriguez.jpg',
                'linkedin_url' => 'https://linkedin.com/in/emilyrodriguez',
                'skills' => json_encode(['UI Design', 'UX Research', 'Prototyping', 'Adobe Creative Suite', 'Figma']),
                'is_active' => true,
                'is_verified' => true,
                'sort_order' => 4,
                'joined_date' => '2020-01-15',
            ],
            [
                'name' => 'David Thompson',
                'role' => 'Digital Marketing Manager',
                'bio' => 'David drives our digital marketing initiatives with a focus on ROI-driven campaigns. He has helped numerous clients achieve significant growth through strategic marketing.',
                'email' => 'david.thompson@ictsolutions.com',
                'phone' => '+1 (555) 123-4505',
                'photo_url' => '/images/team/david-thompson.jpg',
                'linkedin_url' => 'https://linkedin.com/in/davidthompson',
                'twitter_url' => 'https://twitter.com/davidthompson',
                'skills' => json_encode(['SEO', 'Google Ads', 'Social Media Marketing', 'Content Strategy', 'Analytics']),
                'is_active' => true,
                'is_verified' => true,
                'sort_order' => 5,
                'joined_date' => '2018-09-01',
            ],
            [
                'name' => 'Lisa Park',
                'role' => 'Project Manager',
                'bio' => 'Lisa ensures all projects are delivered on time and within budget. Her excellent communication skills and attention to detail make her an invaluable team member.',
                'email' => 'lisa.park@ictsolutions.com',
                'phone' => '+1 (555) 123-4506',
                'photo_url' => '/images/team/lisa-park.jpg',
                'linkedin_url' => 'https://linkedin.com/in/lisapark',
                'skills' => json_encode(['Project Management', 'Agile Methodology', 'Client Relations', 'Quality Assurance']),
                'is_active' => true,
                'is_verified' => true,
                'sort_order' => 6,
                'joined_date' => '2021-02-01',
            ],
        ];

        foreach ($teamMembers as $member) {
            TeamMember::create($member);
        }
    }
}