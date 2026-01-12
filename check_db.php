<?php
use App\Models\User;
use App\Models\Author;
use App\Models\Category;
use App\Models\SubscriptionPackage;
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "User ID 1: " . (User::find(1) ? 'Exists' : 'Missing') . "\n";
echo "Author ID 1: " . (Author::find(1) ? 'Exists' : 'Missing') . "\n";
echo "Category ID 1: " . (Category::find(1) ? 'Exists' : 'Missing') . "\n";
echo "Package ID 1: " . (SubscriptionPackage::find(1) ? 'Exists' : 'Missing') . "\n";

$u = User::where('email', 'readen@example.com')->first();
echo "Debug User: " . ($u ? $u->id : 'Missing') . "\n";
