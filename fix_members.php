<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Organization;
use App\Models\User;

echo "Fixing member records...\n";

$org = Organization::first();
if (! $org) {
    echo "No organization found\n";
    exit;
}

echo "Organization: {$org->name}\n";
echo "Owner ID: {$org->owner_id}\n";

$owner = User::find($org->owner_id);
if (! $owner) {
    echo "Owner not found\n";
    exit;
}

// Create member record for owner if missing
if (! $org->members()->where('user_id', $owner->id)->exists()) {
    $org->members()->create(['user_id' => $owner->id]);
    echo "Created member record for owner: {$owner->name}\n";
} else {
    echo "Owner already has member record\n";
}

// Assign admin role to owner if missing
if (! $owner->hasRole('admin')) {
    $owner->assignRole('admin');
    echo "Assigned admin role to owner\n";
} else {
    echo "Owner already has admin role\n";
}

// Create member record for the accepted user
$acceptedUser = User::where('email', 'vincent.talbot@rebotlabs.com')->first();
if ($acceptedUser && ! $org->members()->where('user_id', $acceptedUser->id)->exists()) {
    $org->members()->create(['user_id' => $acceptedUser->id]);
    echo "Created member record for accepted user: {$acceptedUser->name}\n";
} else {
    echo "Accepted user already has member record or does not exist\n";
}

echo 'Fixed member records. Now members count: '.$org->members()->count()."\n";

echo "\nFinal state:\n";
$org->members()->with('user')->get()->each(function ($member) {
    echo "  - {$member->user->name} ({$member->user->email})\n";
});
