<?php

use App\Events\NewsPublished;
use Illuminate\Support\Facades\Event;
use Illuminate\Events\Dispatcher;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Inspecting Listeners for " . NewsPublished::class . "...\n";

$listeners = Event::getListeners(NewsPublished::class);
echo "Total Listeners: " . count($listeners) . "\n";

foreach ($listeners as $index => $listener) {
    echo "Listener #{$index}:\n";
    if (is_array($listener)) {
        $class = is_object($listener[0]) ? get_class($listener[0]) : $listener[0];
        $method = $listener[1];
        echo "  Type: Array callback\n";
        echo "  Class: $class\n";
        echo "  Method: $method\n";
    } elseif ($listener instanceof Closure) {
        $reflection = new ReflectionFunction($listener);
        echo "  Type: Closure\n";
        echo "  File: " . $reflection->getFileName() . "\n";
        echo "  Line: " . $reflection->getStartLine() . "\n";
        
        // Try to see if it's a bound string listener resolved to closure
        // Laravel wraps string listeners in a closure
        $staticVariables = $reflection->getStaticVariables();
        if (isset($staticVariables['listener'])) {
             echo "  Wrapped Listener: " . $staticVariables['listener'] . "\n";
        }
    } elseif (is_string($listener)) {
        echo "  Type: String\n";
        echo "  Value: $listener\n";
    } else {
        echo "  Type: Unknown\n";
        var_dump($listener);
    }
    echo "--------------------------------------------------\n";
}
