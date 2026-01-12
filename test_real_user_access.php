<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\News;
use App\Models\SubscriptionPackage;
use App\Models\UserSubscription;
use App\Models\Category;
use App\Models\Admin;

echo "Starting Real User Access Test...\n";

// Helper to clean up
SubscriptionPackage::where('slug', 'test-bangla-only')->delete();
User::where('email', 'test_access_user@example.com')->delete();

// 1. Create Test Package
$package = new SubscriptionPackage();
$package->name = 'Test Bangla Only';
$package->slug = 'test-bangla-only';
$package->description = 'Test';
$package->price = 10.00;
$package->currency = 'USD';
$package->billing_period = 'monthly';
$package->access_news = 1;
$package->access_bangla = 1; // Bangla YES
$package->access_english = 0; // English NO
$package->access_images = 0;
$package->access_videos = 0;
$package->access_exclusive = 0;
$package->ad_free = 0;
$package->priority_support = 0;
$package->is_active = 1;
$package->sort_order = 0;
$package->max_articles_per_day = null;
$package->save();

echo "Package ID: {$package->id}, Bangla: {$package->access_bangla}, English: {$package->access_english}\n";

// 2. Create Test User
$user = new User();
$user->name = 'Test Access User';
$user->email = 'test_access_user@example.com';
$user->password = bcrypt('password');
$user->save();

// 3. Assign Subscription
$sub = new UserSubscription();
$sub->user_id = $user->id;
$sub->subscription_package_id = $package->id;
$sub->status = 'active';
$sub->expires_at = now()->addDays(30);
$sub->payment_method = 'manual';
$sub->starts_at = now();
$sub->save();

echo "User ID: {$user->id} assigned to Package: {$package->name}\n";

// Need Category and Author for News (foreign keys)
$category = Category::first();
if (!$category) {
    $category = new Category();
    $category->name = 'Test Cat';
    $category->slug = 'test-cat';
    $category->language = 'en';
    $category->save();
}

$admin = Admin::first();
if (!$admin) {
    $admin = new Admin();
    $admin->name = 'Admin';
    $admin->email = 'admin@test.com';
    $admin->password = bcrypt('password');
    $admin->save();
}

// 4. Test Access
// English News
$newsEn = new News();
$newsEn->language = 'en';
$newsEn->category_id = $category->id;
$newsEn->auther_id = $admin->id;
$newsEn->title = 'Test EN News';
$newsEn->slug = 'test-en-news';
$newsEn->content = 'Content';
$newsEn->subscription_required = 'pro'; 
$newsEn->image = 'test.jpg'; // non-nullable?
$newsEn->save();

echo "Testing access to ENGLISH News (Should be DENIED)...\n";
$accessEn = $user->canAccessNews($newsEn);
echo "Result EN: " . ($accessEn ? 'ALLOWED' : 'DENIED') . "\n";

// Bangla News
$newsBn = new News();
$newsBn->language = 'bn';
$newsBn->category_id = $category->id;
$newsBn->auther_id = $admin->id;
$newsBn->title = 'Test BN News';
$newsBn->slug = 'test-bn-news';
$newsBn->content = 'Content';
$newsBn->subscription_required = 'pro';
$newsBn->image = 'test.jpg';
$newsBn->save();

echo "Testing access to BANGLA News (Should be ALLOWED)...\n";
$accessBn = $user->canAccessNews($newsBn);
echo "Result BN: " . ($accessBn ? 'ALLOWED' : 'DENIED') . "\n";

// Cleanup
$newsEn->delete();
$newsBn->delete();
$sub->delete();
$user->delete();
$package->delete();

echo "Done.\n";
