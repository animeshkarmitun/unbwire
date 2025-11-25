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
