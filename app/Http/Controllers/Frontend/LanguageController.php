<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $languageCode = $request->language_code;
        
        // Validate language code exists in database
        $language = Language::where('lang', $languageCode)
            ->where('status', 1)
            ->first();
        
        if (!$language) {
            return response(['status' => 'error', 'message' => 'Invalid language code'], 400);
        }
        
        // Set language in session
        session(['language' => $languageCode]);
        
        // Set application locale for translations
        App::setLocale($languageCode);

        return response(['status' => 'success']);
    }
}
