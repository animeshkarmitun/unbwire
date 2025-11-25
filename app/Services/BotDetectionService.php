<?php

namespace App\Services;

use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BotDetectionService
{
    /**
     * Comprehensive bot detection with behavioral analysis
     * Returns: 'human', 'bot', or 'scraper'
     */
    public function detectUserType(Request $request, ?Visit $visit = null): string
    {
        $userAgent = $request->userAgent();
        $ip = $request->ip();
        
        // Step 1: Check user agent for obvious bots
        if ($this->isObviousBot($userAgent)) {
            return 'bot';
        }
        
        // Step 2: Behavioral analysis (if we have visit data)
        if ($visit) {
            $behavioralScore = $this->analyzeBehavior($visit, $ip);
            
            if ($behavioralScore >= 0.7) {
                return 'scraper';
            } elseif ($behavioralScore >= 0.5) {
                return 'bot';
            }
        }
        
        // Step 3: Check for suspicious patterns
        if ($this->hasSuspiciousPatterns($request, $ip)) {
            return 'scraper';
        }
        
        // Default: assume human
        return 'human';
    }

    /**
     * Detect obvious bots from user agent
     */
    protected function isObviousBot(?string $userAgent): bool
    {
        if (!$userAgent) {
            return false;
        }
        
        $userAgentLower = strtolower($userAgent);
        
        // Comprehensive list of bot indicators
        $botPatterns = [
            // Common bot keywords
            'bot', 'crawler', 'spider', 'scraper', 'crawling',
            // Search engine bots
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver',
            // Social media bots
            'facebookexternalhit', 'twitterbot', 'linkedinbot', 'pinterest',
            'whatsapp', 'telegrambot', 'skypeuripreview',
            // Other known bots
            'curl', 'wget', 'python-requests', 'java/', 'go-http-client',
            'apache-httpclient', 'okhttp', 'scrapy', 'mechanize',
            // Headless browsers (often used for scraping)
            'headless', 'phantom', 'selenium', 'webdriver', 'puppeteer',
            'playwright', 'chromium', 'chrome-lighthouse',
            // Monitoring tools
            'uptimerobot', 'pingdom', 'monitor', 'check', 'validator',
            // Empty or suspicious user agents
            '', 'mozilla', 'mozilla/4.0', 'mozilla/5.0',
        ];
        
        foreach ($botPatterns as $pattern) {
            if (str_contains($userAgentLower, $pattern)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Analyze behavioral patterns to detect scrapers
     * Returns a score from 0.0 to 1.0 (higher = more likely bot/scraper)
     */
    protected function analyzeBehavior(Visit $visit, string $ip): float
    {
        $score = 0.0;
        
        // 1. Check page views per second (scrapers view pages very quickly)
        if ($visit->duration && $visit->duration > 0) {
            $pagesPerSecond = $visit->page_views_count / $visit->duration;
            if ($pagesPerSecond > 2.0) {
                $score += 0.3; // Very fast page views
            } elseif ($pagesPerSecond > 1.0) {
                $score += 0.15;
            }
        }
        
        // 2. Check session duration (scrapers have very short or very long sessions)
        if ($visit->duration) {
            if ($visit->duration < 5) {
                $score += 0.2; // Too short (likely bot)
            } elseif ($visit->duration > 3600 && $visit->page_views_count < 3) {
                $score += 0.15; // Very long with few page views (suspicious)
            }
        }
        
        // 3. Check bounce rate pattern (scrapers often have high bounce rates)
        if ($visit->page_views_count <= 1 && $visit->duration < 10) {
            $score += 0.15;
        }
        
        // 4. Check for rapid requests from same IP
        $recentVisits = Visit::where('ip_address', $ip)
            ->where('started_at', '>=', now()->subMinutes(5))
            ->count();
        
        if ($recentVisits > 10) {
            $score += 0.2; // Too many visits in short time
        } elseif ($recentVisits > 5) {
            $score += 0.1;
        }
        
        // 5. Check for missing or suspicious user agent
        if (empty($visit->user_agent) || strlen($visit->user_agent) < 10) {
            $score += 0.1;
        }
        
        return min($score, 1.0); // Cap at 1.0
    }

    /**
     * Check for suspicious patterns in the request
     */
    protected function hasSuspiciousPatterns(Request $request, string $ip): bool
    {
        // Check for missing common headers that browsers send
        $requiredHeaders = ['accept', 'accept-language', 'accept-encoding'];
        $missingHeaders = 0;
        
        foreach ($requiredHeaders as $header) {
            if (!$request->header($header)) {
                $missingHeaders++;
            }
        }
        
        // If multiple headers are missing, likely a bot
        if ($missingHeaders >= 2) {
            return true;
        }
        
        // Check for suspicious referrer patterns
        $referrer = $request->header('referer');
        if ($referrer && $this->isSuspiciousReferrer($referrer)) {
            return true;
        }
        
        // Check rate limiting (too many requests from same IP)
        $cacheKey = "bot_detection:rate_limit:{$ip}";
        $requestCount = Cache::get($cacheKey, 0);
        
        if ($requestCount > 50) { // More than 50 requests in 1 minute
            return true;
        }
        
        // Increment rate limit counter
        Cache::put($cacheKey, $requestCount + 1, 60); // 1 minute TTL
        
        return false;
    }

    /**
     * Check if referrer is suspicious
     */
    protected function isSuspiciousReferrer(?string $referrer): bool
    {
        if (!$referrer) {
            return false;
        }
        
        $suspiciousPatterns = [
            'localhost',
            '127.0.0.1',
            'file://',
            'data:',
        ];
        
        foreach ($suspiciousPatterns as $pattern) {
            if (stripos($referrer, $pattern) !== false) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get detailed detection result
     */
    public function getDetectionDetails(Request $request, ?Visit $visit = null): array
    {
        $userType = $this->detectUserType($request, $visit);
        $userAgent = $request->userAgent();
        
        $details = [
            'user_type' => $userType,
            'is_bot' => in_array($userType, ['bot', 'scraper']),
            'is_human' => $userType === 'human',
            'is_scraper' => $userType === 'scraper',
            'detection_method' => [],
        ];
        
        // Add detection reasons
        if ($this->isObviousBot($userAgent)) {
            $details['detection_method'][] = 'user_agent_analysis';
        }
        
        if ($visit) {
            $behavioralScore = $this->analyzeBehavior($visit, $request->ip());
            if ($behavioralScore > 0.5) {
                $details['detection_method'][] = 'behavioral_analysis';
                $details['behavioral_score'] = round($behavioralScore, 2);
            }
        }
        
        if ($this->hasSuspiciousPatterns($request, $request->ip())) {
            $details['detection_method'][] = 'pattern_analysis';
        }
        
        return $details;
    }
}


