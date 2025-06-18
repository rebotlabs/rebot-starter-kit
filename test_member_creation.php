<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Organization;
use App\Models\User;

echo "Testing member creation manually:\n";
$org = Organization::first();
$user = User::where('email', 'test@example.com')->first();

if (! $user) {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    echo "Created test user\n";
}

if (! $org->members()->where('user_id', $user->id)->exists()) {
    $member = $org->members()->create(['user_id' => $user->id]);
    echo "Created member record for test user: {$member->id}\n";
} else {
    echo "Test user already has member record\n";
}

echo 'Total members now: '.$org->members()->count()."\n";
