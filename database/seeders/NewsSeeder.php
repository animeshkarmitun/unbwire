<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Language;
use App\Models\News;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Admin::first();
        if (!$admin) {
            $this->command->error('No admin user found. Please run DatabaseSeeder first.');
            return;
        }

        // Get languages
        $englishLang = Language::where('lang', 'en')->first();
        $banglaLang = Language::where('lang', 'bn')->first();

        if (!$englishLang || !$banglaLang) {
            $this->command->error('Languages not found. Please run DatabaseSeeder first.');
            return;
        }

        // Get categories for both languages
        $enCategories = Category::where('language', 'en')->get()->keyBy('slug');
        $bnCategories = Category::where('language', 'bn')->get()->keyBy('slug');

        // English news articles
        $englishNews = [
            [
                'title' => 'Bangladesh Economy Shows Strong Growth in Q4 2024',
                'category' => 'business',
                'summary' => 'Bangladesh\'s economy demonstrated robust growth in the fourth quarter of 2024, with GDP expanding by 6.8% year-over-year.',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => true,
            ],
            [
                'title' => 'New Climate Change Initiative Launched in Dhaka',
                'category' => 'environment',
                'summary' => 'A comprehensive climate change initiative was launched today in Dhaka, aiming to reduce carbon emissions by 30% by 2030.',
                'is_breaking_news' => true,
                'show_at_slider' => false,
                'show_at_popular' => false,
            ],
            [
                'title' => 'Tech Startups Flourish in Bangladesh',
                'category' => 'tech',
                'summary' => 'Bangladesh\'s tech startup ecosystem is experiencing unprecedented growth, with over 500 new startups launched this year.',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => true,
            ],
            [
                'title' => 'Cricket Team Prepares for Upcoming Test Series',
                'category' => 'sports',
                'summary' => 'The Bangladesh national cricket team is intensifying preparations for the upcoming test series against Australia.',
                'is_breaking_news' => false,
                'show_at_slider' => false,
                'show_at_popular' => true,
            ],
            [
                'title' => 'New Education Policy Approved by Cabinet',
                'category' => 'politics',
                'summary' => 'The cabinet has approved a new education policy focusing on digital literacy and modern teaching methods.',
                'is_breaking_news' => true,
                'show_at_slider' => true,
                'show_at_popular' => false,
            ],
            [
                'title' => 'Healthcare Sector Receives Major Investment',
                'category' => 'business',
                'summary' => 'The healthcare sector in Bangladesh has received a major investment boost, with $500 million allocated for infrastructure development.',
                'is_breaking_news' => false,
                'show_at_slider' => false,
                'show_at_popular' => true,
            ],
            [
                'title' => 'International Film Festival Begins in Dhaka',
                'category' => 'entertainment',
                'summary' => 'The annual international film festival has begun in Dhaka, showcasing films from over 50 countries.',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => false,
            ],
            [
                'title' => 'Bangladesh Signs Trade Agreement with Neighboring Countries',
                'category' => 'world',
                'summary' => 'Bangladesh has signed a new trade agreement with neighboring countries to boost regional economic cooperation.',
                'is_breaking_news' => true,
                'show_at_slider' => false,
                'show_at_popular' => true,
            ],
            [
                'title' => 'Renewable Energy Projects Gain Momentum',
                'category' => 'environment',
                'summary' => 'Renewable energy projects across Bangladesh are gaining momentum, with solar power leading the way.',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => false,
            ],
            [
                'title' => 'Youth Entrepreneurship Program Launched',
                'category' => 'lifestyle',
                'summary' => 'A new youth entrepreneurship program has been launched to support young entrepreneurs in Bangladesh.',
                'is_breaking_news' => false,
                'show_at_slider' => false,
                'show_at_popular' => true,
            ],
        ];

        // Bangla news articles
        $banglaNews = [
            [
                'title' => 'বাংলাদেশের অর্থনীতি ২০২৪ সালের চতুর্থ প্রান্তিকে শক্তিশালী প্রবৃদ্ধি দেখাচ্ছে',
                'category' => 'business',
                'summary' => 'বাংলাদেশের অর্থনীতি ২০২৪ সালের চতুর্থ প্রান্তিকে দৃঢ় প্রবৃদ্ধি প্রদর্শন করেছে, মোট দেশজ উৎপাদন বছরে ৬.৮% বৃদ্ধি পেয়েছে।',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => true,
            ],
            [
                'title' => 'ঢাকায় নতুন জলবায়ু পরিবর্তন উদ্যোগ চালু',
                'category' => 'environment',
                'summary' => 'ঢাকায় আজ একটি ব্যাপক জলবায়ু পরিবর্তন উদ্যোগ চালু করা হয়েছে, যার লক্ষ্য ২০৩০ সালের মধ্যে কার্বন নির্গমন ৩০% কমানো।',
                'is_breaking_news' => true,
                'show_at_slider' => false,
                'show_at_popular' => false,
            ],
            [
                'title' => 'বাংলাদেশে প্রযুক্তি স্টার্টআপের বিকাশ',
                'category' => 'tech',
                'summary' => 'বাংলাদেশের প্রযুক্তি স্টার্টআপ ইকোসিস্টেম অভূতপূর্ব প্রবৃদ্ধি অনুভব করছে, এ বছর ৫০০টিরও বেশি নতুন স্টার্টআপ চালু হয়েছে।',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => true,
            ],
            [
                'title' => 'ক্রিকেট দল আসন্ন টেস্ট সিরিজের জন্য প্রস্তুতি নিচ্ছে',
                'category' => 'sports',
                'summary' => 'বাংলাদেশ জাতীয় ক্রিকেট দল অস্ট্রেলিয়ার বিপক্ষে আসন্ন টেস্ট সিরিজের জন্য প্রস্তুতি তীব্র করছে।',
                'is_breaking_news' => false,
                'show_at_slider' => false,
                'show_at_popular' => true,
            ],
            [
                'title' => 'মন্ত্রিসভা কর্তৃক নতুন শিক্ষানীতি অনুমোদিত',
                'category' => 'politics',
                'summary' => 'মন্ত্রিসভা ডিজিটাল সাক্ষরতা এবং আধুনিক শিক্ষাদান পদ্ধতিতে ফোকাস করে একটি নতুন শিক্ষানীতি অনুমোদন করেছে।',
                'is_breaking_news' => true,
                'show_at_slider' => true,
                'show_at_popular' => false,
            ],
            [
                'title' => 'স্বাস্থ্যসেবা খাতে বড় বিনিয়োগ',
                'category' => 'business',
                'summary' => 'বাংলাদেশের স্বাস্থ্যসেবা খাতে একটি বড় বিনিয়োগ বৃদ্ধি পেয়েছে, অবকাঠামো উন্নয়নের জন্য ৫০০ মিলিয়ন ডলার বরাদ্দ করা হয়েছে।',
                'is_breaking_news' => false,
                'show_at_slider' => false,
                'show_at_popular' => true,
            ],
            [
                'title' => 'ঢাকায় আন্তর্জাতিক চলচ্চিত্র উৎসব শুরু',
                'category' => 'entertainment',
                'summary' => 'বার্ষিক আন্তর্জাতিক চলচ্চিত্র উৎসব ঢাকায় শুরু হয়েছে, ৫০টিরও বেশি দেশের চলচ্চিত্র প্রদর্শন করছে।',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => false,
            ],
            [
                'title' => 'বাংলাদেশ প্রতিবেশী দেশগুলোর সাথে বাণিজ্য চুক্তি স্বাক্ষর করেছে',
                'category' => 'world',
                'summary' => 'বাংলাদেশ আঞ্চলিক অর্থনৈতিক সহযোগিতা বাড়াতে প্রতিবেশী দেশগুলোর সাথে একটি নতুন বাণিজ্য চুক্তি স্বাক্ষর করেছে।',
                'is_breaking_news' => true,
                'show_at_slider' => false,
                'show_at_popular' => true,
            ],
            [
                'title' => 'নবায়নযোগ্য শক্তি প্রকল্প গতি পাচ্ছে',
                'category' => 'environment',
                'summary' => 'বাংলাদেশ জুড়ে নবায়নযোগ্য শক্তি প্রকল্প গতি পাচ্ছে, সৌর শক্তি এগিয়ে রয়েছে।',
                'is_breaking_news' => false,
                'show_at_slider' => true,
                'show_at_popular' => false,
            ],
            [
                'title' => 'যুব উদ্যোক্তা কর্মসূচি চালু',
                'category' => 'lifestyle',
                'summary' => 'বাংলাদেশের তরুণ উদ্যোক্তাদের সমর্থন করার জন্য একটি নতুন যুব উদ্যোক্তা কর্মসূচি চালু করা হয়েছে।',
                'is_breaking_news' => false,
                'show_at_slider' => false,
                'show_at_popular' => true,
            ],
        ];

        // Seed English news
        $this->seedNewsArticles($englishNews, $enCategories, $admin, 'en');

        // Seed Bangla news
        $this->seedNewsArticles($banglaNews, $bnCategories, $admin, 'bn');

        $this->command->info('News seeder completed successfully!');
    }

    private function seedNewsArticles(array $articles, $categories, $admin, string $language): void
    {
        $placeholder = 'admin/assets/img/placeholder.png';
        $orderCounters = [
            'breaking' => 0,
            'slider' => 0,
            'popular' => 0,
        ];

        foreach ($articles as $index => $article) {
            $slug = Str::slug($article['title']);
            $category = $categories->get($article['category']) ?? $categories->first();
            
            // Increment order counters based on flags
            if ($article['is_breaking_news']) {
                $orderCounters['breaking']++;
            }
            if ($article['show_at_slider']) {
                $orderCounters['slider']++;
            }
            if ($article['show_at_popular']) {
                $orderCounters['popular']++;
            }

            $fullContent = $article['summary'] . ' ' . ($language === 'en' 
                ? 'This is a comprehensive report covering all aspects of the story, including expert opinions, official statements, and detailed analysis of the situation. The report provides readers with a complete understanding of the events and their implications. Additional context and background information help readers grasp the full picture of this important development.'
                : 'এটি একটি বিস্তৃত প্রতিবেদন যা গল্পের সমস্ত দিক কভার করে, বিশেষজ্ঞ মতামত, সরকারী বিবৃতি এবং পরিস্থিতির বিশদ বিশ্লেষণ সহ। প্রতিবেদনটি পাঠকদের ঘটনাগুলি এবং তাদের প্রভাব সম্পর্কে সম্পূর্ণ বোঝাপড়া প্রদান করে। অতিরিক্ত প্রসঙ্গ এবং পটভূমি তথ্য পাঠকদের এই গুরুত্বপূর্ণ উন্নয়নের সম্পূর্ণ চিত্র বুঝতে সাহায্য করে।');

            $news = News::updateOrCreate(
                ['slug' => $slug, 'language' => $language],
                [
                    'language' => $language,
                    'category_id' => $category->id,
                    'auther_id' => $admin->id,
                    'image' => $placeholder,
                    'title' => $article['title'],
                    'slug' => $slug,
                    'content' => $fullContent,
                    'meta_title' => $article['title'],
                    'meta_description' => $article['summary'],
                    'is_breaking_news' => $article['is_breaking_news'] ? 1 : 0,
                    'show_at_slider' => $article['show_at_slider'] ? 1 : 0,
                    'show_at_popular' => $article['show_at_popular'] ? 1 : 0,
                    'is_approved' => 1,
                    'status' => 1,
                    'order_position' => $index + 1,
                    'breaking_order' => $article['is_breaking_news'] ? $orderCounters['breaking'] : 0,
                    'slider_order' => $article['show_at_slider'] ? $orderCounters['slider'] : 0,
                    'popular_order' => $article['show_at_popular'] ? $orderCounters['popular'] : 0,
                    'views' => rand(100, 1000),
                    'created_by' => $admin->id,
                    'created_by_type' => 'admin',
                    'updated_by' => $admin->id,
                    'updated_by_type' => 'admin',
                    'approve_by' => $admin->id,
                    'approve_by_type' => 'admin',
                ]
            );
        }
    }
}
