<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\News;
use App\Models\SubscriptionPackage;

echo "Diagnostic Start...\n";

// 1. Get sample news
$newsEn = new News(); $newsEn->language = 'en'; $newsEn->subscription_required = 'pro';
$newsBn = new News(); $newsBn->language = 'bn'; $newsBn->subscription_required = 'pro';

// 2. Iterate all users who have active subscriptions
$users = User::whereHas('subscriptions', function($q) {
    $q->where('status', 'active')->where('expires_at', '>', now());
})->with('subscriptions.package')->get();

foreach ($users as $user) {
    $package = $user->currentPackage();
    if (!$package) continue;

    $canEn = $user->canAccessNews($newsEn);
    $canBn = $user->canAccessNews($newsBn);

    $pkgEn = $package->access_english;
    $pkgBn = $package->access_bangla;

    echo "User: {$user->id} ({$user->name}) \n";
    echo "  Package: {$package->name} (ID: {$package->id})\n";
    echo "  DB Perms -> BN: $pkgBn, EN: $pkgEn \n";
    echo "  Access   -> BN: " . ($canBn?'YES':'NO') . ", EN: " . ($canEn?'YES':'NO') . "\n";
    
    // Alert if mismatch
    if ($pkgEn == 0 && $canEn) {
        echo "  [CRITICAL WARNING] User has access to English but Package denies it!\n";
    }
    echo "--------------------------------------------------\n";
}
echo "Done.\n";
