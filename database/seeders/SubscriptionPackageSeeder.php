<?php

namespace Database\Seeders;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Seeder;

class SubscriptionPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'UNB Lite',
                'slug' => 'unb-lite',
                'description' => 'Access to news articles and images. Perfect for casual readers.',
                'price' => 9.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'access_news' => true,
                'access_images' => true,
                'access_videos' => false,
                'access_exclusive' => false,
                'max_articles_per_day' => null,
                'ad_free' => false,
                'priority_support' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'UNB Pro',
                'slug' => 'unb-pro',
                'description' => 'Access to news, images, and videos. Ideal for engaged readers.',
                'price' => 19.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'access_news' => true,
                'access_images' => true,
                'access_videos' => true,
                'access_exclusive' => false,
                'max_articles_per_day' => null,
                'ad_free' => true,
                'priority_support' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'UNB Ultra',
                'slug' => 'unb-ultra',
                'description' => 'Full access to all content including exclusive articles. Premium experience.',
                'price' => 29.99,
                'currency' => 'USD',
                'billing_period' => 'monthly',
                'access_news' => true,
                'access_images' => true,
                'access_videos' => true,
                'access_exclusive' => true,
                'max_articles_per_day' => null,
                'ad_free' => true,
                'priority_support' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($packages as $package) {
            SubscriptionPackage::updateOrCreate(
                ['slug' => $package['slug']],
                $package
            );
        }
    }
}

