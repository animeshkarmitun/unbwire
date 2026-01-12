<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SubscriptionPackage;

class CheckSubscriptionLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Check valid user and package
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        $package = $user->currentPackage();
        if (!$package) {
             // Let normal subscription checks handle "no package" scenarios
            return $next($request);
        }

        // 2. Get current language and permissions
        $currentLang = getLangauge(); // 'en' or 'bn'
        $accessBangla = (bool) $package->access_bangla;
        $accessEnglish = (bool) $package->access_english;

        // 3. Logic Matrix
        
        // Case A: User has access to current language. All good.
        if (($currentLang == 'en' && $accessEnglish) || ($currentLang == 'bn' && $accessBangla)) {
            return $next($request);
        }

        // Case B: Current denied, but has Bangla access -> Switch to Bangla
        if ($currentLang == 'en' && !$accessEnglish && $accessBangla) {
            setLanguage('bn');
            // We can continue request, but since language is switched, 
            // the view will now render Bangla content.
            // A toast message would be nice.
            toast('Switched to Bangla as you only have access to Bangla content.', 'info');
            return $next($request);
        }

        // Case C: Current denied, but has English access -> Switch to English
        if ($currentLang == 'bn' && !$accessBangla && $accessEnglish) {
            setLanguage('en');
            toast('Switched to English as you only have access to English content.', 'info');
            return $next($request);
        }

        // Case D: No access to either (Both false) -> Redirect to plans
        if (!$accessBangla && !$accessEnglish) {
             // Only redirect if not already on the plans page to avoid loops
            if (!$request->routeIs('subscription.plans')) {
                return redirect()->route('subscription.plans')->with('error', 'Your current package does not allow access to news content.');
            }
        }
        
        // Case E: Fallback (e.g. current is unknown 'fr'?) -> Default behavior
        return $next($request);
    }
}
