<?php

use App\Models\News;
use App\Models\User;
use App\Models\SubscriptionPackage;
use App\Models\UserSubscription;
use App\Events\NewsPublished;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// clear log
file_put_contents(storage_path('logs/laravel.log'), '');

echo "Starting Debug...\n";

// 1. Ensure at least one user has email notifications enabled and a package
$user = User::where('email', 'readen@example.com')->first();
if (!$user) {
    echo "Creating test user...\n";
    // Check if user exists by email, if so use it
    $user = User::where('email', 'readen@example.com')->first();
    if (!$user) {
        $user = new User();
        $user->name = 'Debug User';
        $user->email = 'readen@example.com';
        $user->password = bcrypt('password');
        $user->email_notifications_enabled = true;
        $user->send_full_news_email = true;
        $user->save();
    }
} else {
    $user->update(['email_notifications_enabled' => true]);
}

// Ensure active subscription with FULL access
$package = SubscriptionPackage::firstOrCreate(
    ['slug' => 'debug-package'],
    [
        'name' => 'Debug Package',
        'price' => 10,
        'currency' => 'USD',
        'billing_period' => 'monthly',
        'access_news' => true,
        'access_bangla' => true,
        'access_english' => true,
        'access_images' => true,
        'access_videos' => true,
        'access_exclusive' => true,
        'is_active' => true
    ]
);

UserSubscription::updateOrCreate(
    ['user_id' => $user->id],
    [
        'subscription_package_id' => $package->id,
        'status' => 'active',
        'starts_at' => now(),
        'expires_at' => now()->addMonth()
    ]
);

// Ensure dependencies exist
try {
    $author = \App\Models\Author::firstOrCreate(
        ['name' => 'Test Author'],
        [
            'language' => 'en',
            'designation' => 'Reporter',
            'status' => 1,
            'photo' => 'default.jpg' // Add photo just in case
        ]
    );
} catch (\Exception $e) {
    echo "Error creating author: " . $e->getMessage() . "\n";
    exit;
}

$category = \App\Models\Category::firstOrCreate(
    ['slug' => 'test-category'],
    [
        'name' => 'Test Category',
        'language' => 'en',
        'show_at_home' => 1,
        'status' => 1,
        'order' => 1
    ]
);

// 2. Create a test news item
try {
    $news = News::firstOrCreate(
        ['title' => 'Debug Notification Test ' . time()],
        [
            'slug' => 'debug-notification-test-' . time(),
            'content' => 'This is a test content for debugging notifications.',
            'language' => 'en',
            'category_id' => $category->id,
            'auther_id' => 1, // Admin ID usually 1
            'author_id' => $author->id,
            'status' => 1,
            'is_approved' => 1,
            'is_breaking_news' => 0,
            'show_at_slider' => 0,
            'show_at_popular' => 0,
            'image' => 'default.jpg', // Required field
            'created_at' => now(),
        ]
    );
} catch (\Exception $e) {
    echo "Error creating news: " . $e->getMessage() . "\n";
    exit;
}

echo "News created: {$news->id}\n";
echo "Dispatching event...\n";

// 3. Dispatch Event
try {
    NewsPublished::dispatch($news);
    echo "Event dispatched.\n";
} catch (\Exception $e) {
    echo "Error dispatching event: " . $e->getMessage() . "\n";
}

// 4. Read Log
echo "Reading Log...\n";
$log = file_get_contents(storage_path('logs/laravel.log'));
echo "---------------- LOG START ----------------\n";
echo substr($log, -2000); // Last 2000 chars
echo "---------------- LOG END ----------------\n";
