<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Partners;

echo "Checking partners in database...\n\n";

$count = Partners::count();
echo "Total partners: $count\n\n";

if ($count > 0) {
    echo "Partners list:\n";
    echo "-------------\n";
    Partners::all()->each(function($partner) {
        echo "ID: {$partner->id} | Name: {$partner->name} | Picture: {$partner->picture}\n";
    });
} else {
    echo "No partners found in database.\n";
    echo "Run: php seed_partners.php to add sample data.\n";
}
