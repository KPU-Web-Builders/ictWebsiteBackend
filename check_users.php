<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "Checking for email: loppijj@gmail.com\n";
echo "Total users in database: " . User::count() . "\n\n";

$user = User::where('email', 'loppijj@gmail.com')->first();
if ($user) {
    echo "FOUND: User with email 'loppijj@gmail.com' already exists!\n";
    echo "ID: {$user->id}\n";
    echo "Name: {$user->name}\n";
    echo "Email: {$user->email}\n";
    echo "Created: {$user->created_at}\n\n";
    echo "This is why you're getting the 'email already taken' error.\n";
} else {
    echo "NOT FOUND: Email 'loppijj@gmail.com' does not exist in database.\n";
}

echo "\nAll users:\n";
echo "----------\n";
User::all(['id', 'name', 'email'])->each(function($u) {
    echo "ID: {$u->id} | Name: {$u->name} | Email: {$u->email}\n";
});
