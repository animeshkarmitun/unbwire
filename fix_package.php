<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$p = App\Models\SubscriptionPackage::find(2);
if ($p) {
    $p->access_news = 1;
    $p->save();
    echo "Fixed Package 2 access_news to 1.\n";
} else {
    echo "Package 2 not found.\n";
}
