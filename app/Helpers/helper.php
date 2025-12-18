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

/** Convert English numerals to Bangla numerals */
function toBanglaNumber($number): string
{
    $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $bangla = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
    
    return str_replace($english, $bangla, $number);
}

/** Format date based on language */
function formatDate($date, $format = null): string
{
    // Handle null or empty dates
    if (empty($date) || $date === null) {
        return '';
    }
    
    $language = getLangauge();
    
    try {
        if (is_string($date)) {
            $date = \Carbon\Carbon::parse($date);
        }
        
        // Check if date is valid (not epoch)
        if ($date->timestamp <= 0 || $date->year < 1970) {
            return '';
        }
    } catch (\Exception $e) {
        return '';
    }
    
    if ($language === 'bn') {
        // Bangla date format
        $banglaMonths = [
            'January' => 'জানুয়ারি',
            'February' => 'ফেব্রুয়ারি',
            'March' => 'মার্চ',
            'April' => 'এপ্রিল',
            'May' => 'মে',
            'June' => 'জুন',
            'July' => 'জুলাই',
            'August' => 'আগস্ট',
            'September' => 'সেপ্টেম্বর',
            'October' => 'অক্টোবর',
            'November' => 'নভেম্বর',
            'December' => 'ডিসেম্বর'
        ];
        
        $banglaDays = [
            'Monday' => 'সোমবার',
            'Tuesday' => 'মঙ্গলবার',
            'Wednesday' => 'বুধবার',
            'Thursday' => 'বৃহস্পতিবার',
            'Friday' => 'শুক্রবার',
            'Saturday' => 'শনিবার',
            'Sunday' => 'রবিবার'
        ];
        
        if ($format === 'full') {
            $dayName = $banglaDays[$date->format('l')] ?? $date->format('l');
            $monthName = $banglaMonths[$date->format('F')] ?? $date->format('F');
            $day = toBanglaNumber($date->format('j'));
            $year = toBanglaNumber($date->format('Y'));
            return $dayName . ', ' . $day . ' ' . $monthName . ', ' . $year;
        } else {
            $monthName = $banglaMonths[$date->format('F')] ?? $date->format('F');
            $day = toBanglaNumber($date->format('j'));
            $year = toBanglaNumber($date->format('Y'));
            return $day . ' ' . $monthName . ', ' . $year;
        }
    }
    
    // English format
    if ($format === 'full') {
        return $date->format('l, F j, Y');
    } else {
        return $date->format('M d, Y');
    }
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

/** Normalize footer URL to ensure it's absolute */
if (!function_exists('normalizeFooterUrl')) {
    function normalizeFooterUrl(string $url): string
    {
        // If it's already an external URL or absolute path, return as is
        if (substr($url, 0, 7) === 'http://' || substr($url, 0, 8) === 'https://' || substr($url, 0, 1) === '/') {
            return $url;
        }
        
        // Otherwise, make it an absolute path by prepending '/'
        return '/' . ltrim($url, '/');
    }
}
