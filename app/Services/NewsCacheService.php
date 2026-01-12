<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

class NewsCacheService
{
    protected $config;

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Load configuration from app-config.json
     */
    protected function loadConfig()
    {
        $path = base_path('app-config.json');
        if (File::exists($path)) {
            $this->config = json_decode(File::get($path), true);
        }
    }

    /**
     * Get TTL for a specific cache type
     */
    protected function getTtl($type)
    {
        // Default TTLs if config is missing
        $defaults = [
            'article' => 600, // 10 minutes
            'headlines' => 3600 // 1 hour
        ];

        return $this->config['cache'][$type]['ttl'] ?? $defaults[$type];
    }

    /**
     * Get article from cache or DB
     * 
     * @param int $id
     * @param callable $fetchCallback Function to fetch data if cache miss
     * @return mixed
     */
    public function getArticle($id, callable $fetchCallback)
    {
        $key = "article:{$id}";
        $ttl = $this->getTtl('article');

        if (Cache::has($key)) {
            Log::info("Cache HIT for {$key}");
            return Cache::get($key);
        }

        Log::info("Cache MISS for {$key}");
        
        $data = $fetchCallback();

        if ($data) {
            Cache::put($key, $data, $ttl);
        }

        return $data;
    }

    /**
     * Get headlines (lists) from cache or DB using versioning
     * 
     * @param string $section e.g., 'breaking:en:free', 'latest'
     * @param callable $fetchCallback Function to fetch data if cache miss
     * @return mixed
     */
    public function getHeadlines($section, callable $fetchCallback)
    {
        // Extract the base section type to use for versioning
        // e.g. "breaking:en:free" -> base "breaking"
        $parts = explode(':', $section);
        $baseType = $parts[0];

        // Get current version for this section type
        $versionKey = "version:headlines:{$baseType}";
        $version = Cache::get($versionKey, 1);

        // Construct versioned key
        $key = "headlines:{$section}:v{$version}";
        $ttl = $this->getTtl('headlines');

        if (Cache::has($key)) {
            Log::info("Cache HIT for {$key}");
            return Cache::get($key);
        }

        Log::info("Cache MISS for {$key}");

        $data = $fetchCallback();

        if ($data) {
            Cache::put($key, $data, $ttl);
        }

        return $data;
    }

    /**
     * Invalidate article cache and related headlines
     * 
     * @param int $id
     */
    public function invalidateArticle($id)
    {
        $key = "article:{$id}";
        Cache::forget($key);
        Log::info("Cache INVALIDATED for {$key}");

        // Bump versions for related headline sections
        // This validates all variants (lang/tier) of these lists
        $sections = ['breaking', 'latest', 'popular', 'slider', 'recent'];
        foreach ($sections as $section) {
            Cache::increment("version:headlines:{$section}");
            Log::info("Cache VERSION BUMP for headlines:{$section}");
        }
    }
    
    /**
     * Extended invalidation with context
     */
    public function invalidateArticleWithContext($news)
    {
        if (!$news) return;

        $this->invalidateArticle($news->id);
        
        // Also bump category lists if handled
        if ($news->category_id) {
             // We don't cache category lists via service yet in HomeController, 
             // but if we did, we'd bump "version:headlines:category"
             // For now, only main sections are cached.
        }
    }
}
