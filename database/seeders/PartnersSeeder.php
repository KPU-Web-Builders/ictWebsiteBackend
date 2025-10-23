<?php

namespace Database\Seeders;

use App\Models\Partners;
use Illuminate\Database\Seeder;

class PartnersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing partners to avoid duplicates
        Partners::truncate();

        $partners = [
            [
                'name' => 'Microsoft',
                'picture' => 'https://logo.clearbit.com/microsoft.com',
            ],
            [
                'name' => 'Google',
                'picture' => 'https://logo.clearbit.com/google.com',
            ],
            [
                'name' => 'Amazon Web Services',
                'picture' => 'https://logo.clearbit.com/aws.amazon.com',
            ],
            [
                'name' => 'IBM',
                'picture' => 'https://logo.clearbit.com/ibm.com',
            ],
            [
                'name' => 'Oracle',
                'picture' => 'https://logo.clearbit.com/oracle.com',
            ],
            [
                'name' => 'Cisco',
                'picture' => 'https://logo.clearbit.com/cisco.com',
            ],
            [
                'name' => 'Dell Technologies',
                'picture' => 'https://logo.clearbit.com/dell.com',
            ],
            [
                'name' => 'HP',
                'picture' => 'https://logo.clearbit.com/hp.com',
            ],
            [
                'name' => 'Intel',
                'picture' => 'https://logo.clearbit.com/intel.com',
            ],
            [
                'name' => 'VMware',
                'picture' => 'https://logo.clearbit.com/vmware.com',
            ],
            [
                'name' => 'Adobe',
                'picture' => 'https://logo.clearbit.com/adobe.com',
            ],
            [
                'name' => 'Salesforce',
                'picture' => 'https://logo.clearbit.com/salesforce.com',
            ],
            [
                'name' => 'SAP',
                'picture' => 'https://logo.clearbit.com/sap.com',
            ],
            [
                'name' => 'Nvidia',
                'picture' => 'https://logo.clearbit.com/nvidia.com',
            ],
            [
                'name' => 'Slack',
                'picture' => 'https://logo.clearbit.com/slack.com',
            ],
        ];

        foreach ($partners as $partner) {
            Partners::create($partner);
        }
    }
}
