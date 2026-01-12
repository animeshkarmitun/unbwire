<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Mock classes
class MockPackage {
    public $id = 1;
    public $access_bangla;
    public $access_english;
    public $slug = 'unb-pro';
    
    public function __construct($bangla, $english) {
        $this->access_bangla = $bangla;
        $this->access_english = $english;
    }

    public function hasAccess($feature) {
        if ($feature === 'news') return true;
        if ($feature === 'exclusive') return false;
        return false;
    }
}

class MockUser {
    public $package;
    
    public function currentPackage() {
        return $this->package;
    }
    
    // Copy of canAccessNews method logic from User.php
    public function canAccessNews($news)
    {
        $package = $this->currentPackage();
        
        if (!$package) {
            return false;
        }

        if (!$package->hasAccess('news')) {
            return false;
        }

        if ($news->language) {
            $newsLang = strtolower(trim($news->language));
            
            // In real code, it reloads from DB. Here we use the object properties.
            $hasBanglaAccess = (bool) $package->access_bangla;
            $hasEnglishAccess = (bool) $package->access_english;
            
            echo "Checking Language: $newsLang. Access - Bangla: " . ($hasBanglaAccess ? 'Y' : 'N') . ", English: " . ($hasEnglishAccess ? 'Y' : 'N') . "\n";

            if ($hasBanglaAccess || $hasEnglishAccess) {
                $langAllowed = match($newsLang) {
                    'bn', 'bangla' => $hasBanglaAccess,
                    'en', 'english' => $hasEnglishAccess,
                    default => false,
                };
                
                if (!$langAllowed) {
                    echo "Denied by language restriction.\n";
                    return false;
                }
            } else {
                echo "Both false - allowing all.\n";
            }
        }

        return true;
    }
}

class MockNews {
    public $language;
    public $subscription_required = 'pro';
    public $is_exclusive = false;
    
    public function __construct($lang) {
        $this->language = $lang;
    }
}

// Test Case 1: Bangla Access Only, accessing English News
echo "Test 1: Bangla Access Only -> English News\n";
$user = new MockUser();
$user->package = new MockPackage(true, false); // Bangla: True, English: False
$news = new MockNews('en');
$result = $user->canAccessNews($news);
echo "Result: " . ($result ? 'ALLOWED' : 'DENIED') . "\n\n";

// Test Case 2: Bangla Access Only, accessing Bangla News
echo "Test 2: Bangla Access Only -> Bangla News\n";
$news = new MockNews('bn');
$result = $user->canAccessNews($news);
echo "Result: " . ($result ? 'ALLOWED' : 'DENIED') . "\n\n";

// Test Case 3: Both False (Bug?), accessing English News
echo "Test 3: Both False -> English News\n";
$user->package = new MockPackage(false, false);
$news = new MockNews('en');
$result = $user->canAccessNews($news);
echo "Result: " . ($result ? 'ALLOWED' : 'DENIED') . "\n\n";

// Test Case 4: English Access Only, accessing Bangla News
echo "Test 4: English Access Only -> Bangla News\n";
$user->package = new MockPackage(false, true);
$news = new MockNews('bn');
$result = $user->canAccessNews($news);
echo "Result: " . ($result ? 'ALLOWED' : 'DENIED') . "\n\n";

