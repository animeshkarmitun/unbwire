<?php
use App\Models\News;
use App\Models\User;
use App\Events\NewsPublished;
use Illuminate\Support\Facades\Log;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Clear log
file_put_contents(storage_path('logs/laravel.log'), '');

echo "Starting Debug V2...\n";
Log::info("DEBUG SCRIPT DIRECT LOG CHECK");

$val = \App\Models\Setting::where('key', 'notification_send_to_all')->value('value');
echo "DB Setting notification_send_to_all: " . json_encode($val) . "\n";
echo "Default should be '1'\n";

// Use User 10 (readen@example.com) for testing
$user = User::find(10);
if ($user) {
    $user->email_notifications_enabled = true;
    $user->send_full_news_email = true;
    $user->save();
    echo "User 10 configured.\n";
} else {
    echo "User 10 not found!\n";
    exit;
}

// Create News using existing IDs
try {
    $news = new News();
    $news->title = 'Debug V2 ' . time();
    $news->slug = 'debug-v2-' . time();
    $news->content = 'Content ' . time();
    $news->language = 'en';
    $news->category_id = 1;
    $news->auther_id = 1;
    $news->author_id = 1;
    $news->image = 'default.jpg';
    $news->status = 1;
    $news->is_approved = 1;
    $news->is_breaking_news = 0;
    $news->show_at_slider = 0;
    $news->show_at_popular = 0;
    $news->save();

    echo "News created: {$news->id}\n";
    
    // Dispatch with options
    echo "Dispatching event with force_send_email=true...\n";
    NewsPublished::dispatch($news, ['send_emails' => true]);
    echo "Event dispatched.\n";

    // MANUAL TEST OF LISTENER
    echo "Manual Listener Test...\n";
    $service = $app->make(\App\Services\UserNotificationService::class);
    $listener = new \App\Listeners\SendSubscriberNotifications($service);
    $event = new \App\Events\NewsPublished($news);
    $listener->handle($event);
    echo "Manual Listener Test Done.\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (method_exists($e, 'validator')) {
         print_r($e->validator->errors()->toArray());
    }
}

// Read Log
echo "Reading Log...\n";
$log = file_get_contents(storage_path('logs/laravel.log'));
echo "---------------- LOG START ----------------\n";
echo substr($log, -2000); 
echo "---------------- LOG END ----------------\n";
