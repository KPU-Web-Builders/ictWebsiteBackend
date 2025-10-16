<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "This script will delete the user with email: loppijj@gmail.com\n";
echo "Current total users: " . User::count() . "\n\n";

$user = User::where('email', 'loppijj@gmail.com')->first();
if ($user) {
    echo "Found user:\n";
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n\n";

    $user->delete();
    echo "✓ User deleted successfully!\n";
    echo "New total users: " . User::count() . "\n";
    echo "\nYou can now register with loppijj@gmail.com again.\n";
} else {
    echo "No user found with email 'loppijj@gmail.com'\n";
}
