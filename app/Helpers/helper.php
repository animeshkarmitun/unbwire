<?php

use App\Models\Language;
use App\Models\Setting;
use PhpParser\Node\Expr\Cast\String_;

/** format news tags */

function formatTags(array $tags): String
{
   return implode(',', $tags);
}

/** get selected language from session */
function getLangauge(): string
{
    if(session()->has('language')){
        return session('language');
    }else {
        try {
            $language = Language::where('default', 1)->first();
            
            if($language && $language->lang) {
                setLanguage($language->lang);
                return $language->lang;
            }
            
            // If no default language found, set and return 'en'
            setLanguage('en');
            return 'en';
        } catch (\Throwable $th) {
            setLanguage('en');
            return 'en';
        }
    }
}

/** set language code in session */
function setLanguage(string $code): void
{
    session(['language' => $code]);
}

/** Truncate text */

function truncate(string $text, int $limit = 45): String
{
    return \Str::limit($text, $limit, '...');
}

/** Convert a number in K format */

function convertToKFormat(int $number): String
{
    if($number < 1000){
        return $number;
    }elseif($number < 1000000){
        return round($number / 1000, 1) . 'K';
    }else {
        return round($number / 1000000, 1). 'M';
    }
}

/** Make Sidebar Active */

function setSidebarActive(array $routes): ?string
{
    foreach($routes as $route){
        if(request()->routeIs($route)){
            return 'active';
        }
    }
    return '';
}

/** get Setting */

function getSetting($key){
    $data = Setting::where('key', $key)->first();
    return $data ? $data->value : null;
}

/** check permission */

function canAccess(array $permissions){

   $permission = auth()->guard('admin')->user()->hasAnyPermission($permissions);
   $superAdmin = auth()->guard('admin')->user()->hasRole('Super Admin');

   if($permission || $superAdmin){
    return true;
   }else {
    return false;
   }

}

/** get admin role */

function getRole(){
    $role = auth()->guard('admin')->user()->getRoleNames();
    return $role->first();
}

/** check user permission */

function checkPermission(string $permission){
    return auth()->guard('admin')->user()->hasPermissionTo($permission);
}

/** Get language name in native script */
function getLanguageNativeName(string $langCode): string
{
    $nativeNames = [
        'bn' => 'বাংলা',
        'en' => 'English',
        'ar' => 'العربية',
        'hi' => 'हिन्दी',
        'ur' => 'اردو',
        // Add more languages as needed
    ];

    return $nativeNames[$langCode] ?? $langCode;
}

/** Format date based on language */
function formatDate($date, string $format = 'M d, Y'): string
{
    if (!$date) {
        return '';
    }

    // Convert to Carbon instance if it's a string
    if (is_string($date)) {
        $date = \Carbon\Carbon::parse($date);
    } elseif (!$date instanceof \Carbon\Carbon) {
        $date = \Carbon\Carbon::parse($date);
    }

    $language = getLangauge();

    // If language is Bengali, translate month and day names
    if ($language === 'bn') {
        // Bengali month names (full)
        $bengaliMonths = [
            'January' => 'জানুয়ারি', 'February' => 'ফেব্রুয়ারি', 'March' => 'মার্চ', 'April' => 'এপ্রিল',
            'May' => 'মে', 'June' => 'জুন', 'July' => 'জুলাই', 'August' => 'আগস্ট',
            'September' => 'সেপ্টেম্বর', 'October' => 'অক্টোবর', 'November' => 'নভেম্বর', 'December' => 'ডিসেম্বর'
        ];

        // Bengali short month names
        $bengaliShortMonths = [
            'Jan' => 'জানু', 'Feb' => 'ফেব্রু', 'Mar' => 'মার্চ', 'Apr' => 'এপ্রিল',
            'May' => 'মে', 'Jun' => 'জুন', 'Jul' => 'জুলাই', 'Aug' => 'আগস্ট',
            'Sep' => 'সেপ্ট', 'Oct' => 'অক্টো', 'Nov' => 'নভে', 'Dec' => 'ডিসে'
        ];

        // Bengali day names (full)
        $bengaliDays = [
            'Sunday' => 'রবিবার', 'Monday' => 'সোমবার', 'Tuesday' => 'মঙ্গলবার',
            'Wednesday' => 'বুধবার', 'Thursday' => 'বৃহস্পতিবার', 'Friday' => 'শুক্রবার', 'Saturday' => 'শনিবার'
        ];

        // Bengali short day names
        $bengaliShortDays = [
            'Sun' => 'রবি', 'Mon' => 'সোম', 'Tue' => 'মঙ্গল',
            'Wed' => 'বুধ', 'Thu' => 'বৃহ', 'Fri' => 'শুক্র', 'Sat' => 'শনি'
        ];

        // Get English formatted date
        $formatted = $date->format($format);
        
        // Replace full month names (F)
        $englishMonth = $date->format('F');
        if (isset($bengaliMonths[$englishMonth])) {
            $formatted = str_ireplace($englishMonth, $bengaliMonths[$englishMonth], $formatted);
        }
        
        // Replace short month names (M)
        $englishShortMonth = $date->format('M');
        if (isset($bengaliShortMonths[$englishShortMonth])) {
            $formatted = str_ireplace($englishShortMonth, $bengaliShortMonths[$englishShortMonth], $formatted);
        }
        
        // Replace full day names (l)
        $englishDay = $date->format('l');
        if (isset($bengaliDays[$englishDay])) {
            $formatted = str_ireplace($englishDay, $bengaliDays[$englishDay], $formatted);
        }
        
        // Replace short day names (D)
        $englishShortDay = $date->format('D');
        if (isset($bengaliShortDays[$englishShortDay])) {
            $formatted = str_ireplace($englishShortDay, $bengaliShortDays[$englishShortDay], $formatted);
        }

        // Convert numbers to Bengali numerals
        $bengaliNumerals = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
        $englishNumerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        // Replace all English numerals with Bengali numerals
        $formatted = str_replace($englishNumerals, $bengaliNumerals, $formatted);

        return $formatted;
    }

    // For other languages, use standard PHP date formatting
    return $date->format($format);
}

/** Toast notification helper for SweetAlert */

if (!function_exists('toast')) {
    function toast(string $message, string $type = 'success', int $timer = 3000)
    {
        $config = [
            'title' => $message,
            'icon' => $type,
            'timer' => $timer,
            'showConfirmButton' => false,
            'toast' => true,
            'position' => 'top-end',
        ];

        session()->flash('alert.config', json_encode($config));

        return new class($config) {
            private $config;

            public function __construct(array $config)
            {
                $this->config = $config;
            }

            public function width(int $width)
            {
                $this->config['width'] = $width . 'px';
                session()->flash('alert.config', json_encode($this->config));
                return $this;
            }
        };
    }
}
