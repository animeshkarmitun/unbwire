<?php
use App\Events\NewsPublished;
use Illuminate\Support\Facades\Event;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$listeners = Event::getListeners(NewsPublished::class);
echo "COUNT: " . count($listeners) . "\n";
