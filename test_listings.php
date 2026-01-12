<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\News;
use App\Models\SubscriptionPackage;
use App\Models\UserSubscription;

echo "Testing Listings Scope...\n";

// 1. Setup Data
// Packages
$pkgBn = SubscriptionPackage::updateOrCreate(['slug' => 'test-pkg-bn'], [
    'name' => 'Test BN Only', 
    'access_bangla' => 1, 
    'access_english' => 0, 
    'access_news' => 1, 
    'price' => 1,
    'currency' => 'USD',
    'billing_period' => 'monthly',
    'is_active' => 1
]);
$pkgEn = SubscriptionPackage::updateOrCreate(['slug' => 'test-pkg-en'], [
    'name' => 'Test EN Only', 
    'access_bangla' => 0, 
    'access_english' => 1, 
    'access_news' => 1, 
    'price' => 1,
    'currency' => 'USD',
    'billing_period' => 'monthly',
    'is_active' => 1
]);

// Users
$userBn = User::updateOrCreate(['email' => 'test_bn@test.com'], ['name' => 'BN User', 'password' => bcrypt('x')]);
$userEn = User::updateOrCreate(['email' => 'test_en@test.com'], ['name' => 'EN User', 'password' => bcrypt('x')]);

// Subscriptions
UserSubscription::updateOrCreate(['user_id' => $userBn->id], [
    'subscription_package_id' => $pkgBn->id, 'status' => 'active', 'expires_at' => now()->addDay(), 'starts_at' => now()
]);
UserSubscription::updateOrCreate(['user_id' => $userEn->id], [
    'subscription_package_id' => $pkgEn->id, 'status' => 'active', 'expires_at' => now()->addDay(), 'starts_at' => now()
]);

// Ensure News Exists
News::updateOrCreate(['slug' => 'test-news-bn'], ['language' => 'bn', 'title' => 'BN News', 'content' => '.', 'auther_id' => 1, 'category_id' => 1]);
News::updateOrCreate(['slug' => 'test-news-en'], ['language' => 'en', 'title' => 'EN News', 'content' => '.', 'auther_id' => 1, 'category_id' => 1]);

// 2. Test BN User Scope
echo "\nTesting BN User (Package: {$pkgBn->name})\n";
$bnNews = News::forUserLanguage($userBn)->pluck('language')->unique()->toArray();
echo "Languages visible: " . implode(', ', $bnNews) . "\n";

if (in_array('en', $bnNews)) {
    echo "[FAIL] BN User can see English news!\n";
} elseif (!in_array('bn', $bnNews)) {
    echo "[FAIL] BN User CANNOT see Bangla news!\n";
} else {
    echo "[PASS] BN User sees only Bangla.\n";
}

// 3. Test EN User Scope
echo "\nTesting EN User (Package: {$pkgEn->name})\n";
$enNews = News::forUserLanguage($userEn)->pluck('language')->unique()->toArray();
echo "Languages visible: " . implode(', ', $enNews) . "\n";

if (in_array('bn', $enNews)) {
    echo "[FAIL] EN User can see Bangla news!\n";
} elseif (!in_array('en', $enNews)) {
     echo "[FAIL] EN User CANNOT see English news!\n";
} else {
    echo "[PASS] EN User sees only English.\n";
}

// Cleanup
UserSubscription::whereIn('user_id', [$userBn->id, $userEn->id])->delete();
$userBn->delete(); $userEn->delete();
$pkgBn->delete(); $pkgEn->delete();
News::whereIn('slug', ['test-news-bn', 'test-news-en'])->delete();

echo "\nDone.\n";
