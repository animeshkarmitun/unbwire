<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GeolocationService
{
    /**
     * Get location information from IP address
     * Using free ip-api.com service (no API key required)
     */
    public function getLocationFromIp(string $ip, string $context = 'analytics'): array
    {
        // Check if whois functionality is enabled for the context
        $settingKey = $context === 'activity_log' 
            ? 'activity_log_whois_enabled' 
            : 'analytics_whois_enabled';
        
        $whoisEnabled = \App\Models\Setting::where('key', $settingKey)->value('value') ?? '1';
        
        if ($whoisEnabled !== '1') {
            return [
                'country' => null,
                'country_code' => null,
                'city' => null,
            ];
        }
        
        // Skip private/local IPs
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return [
                'country' => null,
                'country_code' => null,
                'city' => null,
            ];
        }
        
        $cacheKey = "geolocation:{$ip}";
        
        return Cache::remember($cacheKey, 86400, function () use ($ip) {
            try {
                $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}", [
                    'fields' => 'status,country,countryCode,city',
                ]);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'success') {
                        return [
                            'country' => $data['country'] ?? null,
                            'country_code' => $data['countryCode'] ?? null,
                            'city' => $data['city'] ?? null,
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Log error if needed
                \Log::warning("Geolocation lookup failed for IP: {$ip}", ['error' => $e->getMessage()]);
            }
            
            return [
                'country' => null,
                'country_code' => null,
                'city' => null,
            ];
        });
    }
}

