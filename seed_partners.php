<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Partners;

echo "Seeding partners table...\n\n";

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
];

$created = 0;
$skipped = 0;

foreach ($partners as $partner) {
    $exists = Partners::where('name', $partner['name'])->first();

    if ($exists) {
        echo "⊘ Skipped: {$partner['name']} (already exists)\n";
        $skipped++;
    } else {
        Partners::create($partner);
        echo "✓ Created: {$partner['name']}\n";
        $created++;
    }
}

echo "\n";
echo "Summary:\n";
echo "--------\n";
echo "Created: $created partners\n";
echo "Skipped: $skipped partners\n";
echo "Total in database: " . Partners::count() . " partners\n";
