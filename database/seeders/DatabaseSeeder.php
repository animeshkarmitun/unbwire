<?php

namespace Database\Seeders;

use App\Models\About;
use App\Models\Ad;
use App\Models\Admin;
use App\Models\Category;
use App\Models\Contact;
use App\Models\FooterGridOne;
use App\Models\FooterGridThree;
use App\Models\FooterGridTwo;
use App\Models\FooterInfo;
use App\Models\FooterTitle;
use App\Models\HomeSectionSetting;
use App\Models\Language;
use App\Models\News;
use App\Models\Setting;
use App\Models\SocialCount;
use App\Models\SocialLink;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Model::unguard();

        DB::transaction(function () {
            $this->seedLanguages();
            $this->seedSettings();
            $admin = $this->seedAdminUser();
            $this->seedDefaultUser();
            $this->seedPermissions($admin);
            $this->call(SubscriptionPackageSeeder::class);

            $categoryMapEn = $this->seedCategories('en');
            $categoryMapBn = $this->seedCategories('bn');
            $tagMapEn = $this->seedTags('en');
            $tagMapBn = $this->seedTags('bn');
            $this->seedHomeSectionSettings($categoryMapEn, $categoryMapBn);
            $this->seedStaticPages();
            $this->seedFooter();
            $this->seedSocialWidgets();
            $this->seedAds();
            $this->seedArticles($categoryMapEn, $tagMapEn, $admin, 'en');
            $this->seedArticles($categoryMapBn, $tagMapBn, $admin, 'bn');
        });

        Model::reguard();
    }

    private function seedLanguages(): void
    {
        // English
        Language::updateOrCreate(
            ['lang' => 'en'],
            [
                'name' => 'English',
                'slug' => 'english',
                'default' => true,
                'status' => true,
            ]
        );

        // Bangla
        Language::updateOrCreate(
            ['lang' => 'bn'],
            [
                'name' => 'Bangla',
                'slug' => 'bangla',
                'default' => false,
                'status' => true,
            ]
        );
    }

    private function seedSettings(): void
    {
        $settings = [
            'site_name' => 'United News of Bangladesh',
            'site_logo' => 'frontend/assets/images/logo1.png',
            'site_favicon' => 'frontend/assets/images/logo2.png',
            'site_seo_title' => 'United News of Bangladesh | Latest online news Bangladesh',
            'site_seo_description' => 'United News of Bangladesh (UNB) - Latest news, breaking news, sports, tech, business, entertainment, politics, world news from Bangladesh and around the world.',
            'site_seo_keywords' => 'bangladesh news,unb news,latest news,breaking news,bangladesh,news portal,daily news',
            'site_color' => '#ED1C24',
            'site_microsoft_api_host' => 'https://api.cognitive.microsoft.com',
            'site_microsoft_api_key' => 'demo-microsoft-api-key',
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }

    private function seedAdminUser(): Admin
    {
        $admin = Admin::updateOrCreate(
            ['email' => 'admin@unb.com.bd'],
            [
                'name' => 'Super Admin',
                'image' => 'frontend/assets/images/avatar.png',
                'password' => Hash::make('password'),
                'status' => true,
            ]
        );

        return $admin;
    }

    private function seedDefaultUser(): void
    {
        User::updateOrCreate(
            ['email' => 'reader@example.com'],
            [
                'name' => 'Demo Reader',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }

    private function seedPermissions(Admin $admin): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'news index',
            'news create',
            'news update',
            'news delete',
            'news all-access',
            'footer index',
            'footer create',
            'footer update',
            'footer destroy',
            'social count index',
            'social count create',
            'social count update',
            'social count delete',
            'access management index',
            'access management create',
            'access management update',
            'access management destroy',
            'subscribers index',
            'subscribers delete',
            'languages index',
            'languages create',
            'languages update',
            'languages delete',
            'about index',
            'about update',
            'contact index',
            'contact update',
            'contact message index',
            'contact message update',
            'advertisement index',
            'advertisement update',
            'subscription package index',
            'subscription package create',
            'subscription package update',
            'subscription package delete',
            'analytics index',
            'analytics view',
            'analytics export',
            'activity log index',
            'activity log view',
            'activity log restore',
            'activity log export',
            'image gallery index',
            'image gallery create',
            'image gallery update',
            'image gallery delete',
            'video gallery index',
            'video gallery create',
            'video gallery update',
            'video gallery delete',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'admin',
            ]);
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $role->syncPermissions(Permission::where('guard_name', 'admin')->get());

        $admin->syncRoles([$role->name]);
    }

    /**
     * @return array<string, int>
     */
    private function seedCategories(string $language): array
    {
        if ($language === 'en') {
            $categories = [
                ['name' => 'Bangladesh', 'slug' => 'bangladesh', 'show_at_nav' => true],
                ['name' => 'World', 'slug' => 'world', 'show_at_nav' => true],
                ['name' => 'Politics', 'slug' => 'politics', 'show_at_nav' => true],
                ['name' => 'Business', 'slug' => 'business', 'show_at_nav' => true],
                ['name' => 'Sports', 'slug' => 'sports', 'show_at_nav' => true],
                ['name' => 'Tech', 'slug' => 'tech', 'show_at_nav' => true],
                ['name' => 'Entertainment', 'slug' => 'entertainment', 'show_at_nav' => true],
                ['name' => 'Lifestyle', 'slug' => 'lifestyle', 'show_at_nav' => true],
                ['name' => 'Opinion', 'slug' => 'opinion', 'show_at_nav' => true],
                ['name' => 'Environment', 'slug' => 'environment', 'show_at_nav' => false],
                ['name' => 'Fact-Checking', 'slug' => 'fact-checking', 'show_at_nav' => false],
            ];
        } else {
            // Bangla categories
            $categories = [
                ['name' => 'বাংলাদেশ', 'slug' => 'bangladesh', 'show_at_nav' => true],
                ['name' => 'বিশ্ব', 'slug' => 'world', 'show_at_nav' => true],
                ['name' => 'রাজনীতি', 'slug' => 'politics', 'show_at_nav' => true],
                ['name' => 'ব্যবসা', 'slug' => 'business', 'show_at_nav' => true],
                ['name' => 'খেলাধুলা', 'slug' => 'sports', 'show_at_nav' => true],
                ['name' => 'প্রযুক্তি', 'slug' => 'tech', 'show_at_nav' => true],
                ['name' => 'বিনোদন', 'slug' => 'entertainment', 'show_at_nav' => true],
                ['name' => 'লাইফস্টাইল', 'slug' => 'lifestyle', 'show_at_nav' => true],
                ['name' => 'মতামত', 'slug' => 'opinion', 'show_at_nav' => true],
                ['name' => 'পরিবেশ', 'slug' => 'environment', 'show_at_nav' => false],
                ['name' => 'ফ্যাক্ট-চেক', 'slug' => 'fact-checking', 'show_at_nav' => false],
            ];
        }

        $map = [];
        foreach ($categories as $category) {
            $record = Category::updateOrCreate(
                ['slug' => $category['slug'], 'language' => $language],
                [
                    'language' => $language,
                    'name' => $category['name'],
                    'slug' => $category['slug'],
                    'show_at_nav' => $category['show_at_nav'],
                    'status' => true,
                ]
            );

            $map[$category['slug']] = $record->id;
        }

        return $map;
    }

    /**
     * @return array<string, int>
     */
    private function seedTags(string $language): array
    {
        if ($language === 'en') {
            $tags = [
                'Earthquake', 'Dhaka', 'Bangladesh', 'Politics', 'Election', 'Cricket', 'Football',
                'Technology', 'Business', 'Health', 'Education', 'Environment', 'Breaking News',
                'International', 'Local', 'Sports', 'Entertainment', 'Lifestyle'
            ];
        } else {
            $tags = [
                'ভূমিকম্প', 'ঢাকা', 'বাংলাদেশ', 'রাজনীতি', 'নির্বাচন', 'ক্রিকেট', 'ফুটবল',
                'প্রযুক্তি', 'ব্যবসা', 'স্বাস্থ্য', 'শিক্ষা', 'পরিবেশ', 'ব্রেকিং নিউজ',
                'আন্তর্জাতিক', 'স্থানীয়', 'খেলাধুলা', 'বিনোদন', 'লাইফস্টাইল'
            ];
        }

        $map = [];
        foreach ($tags as $name) {
            $tag = Tag::updateOrCreate(
                ['name' => $name, 'language' => $language],
                ['language' => $language, 'name' => $name]
            );
            $map[$name] = $tag->id;
        }

        return $map;
    }

    private function seedHomeSectionSettings(array $categoryMapEn, array $categoryMapBn): void
    {
        // English
        HomeSectionSetting::updateOrCreate(
            ['language' => 'en'],
            [
                'category_section_one' => $categoryMapEn['bangladesh'] ?? array_values($categoryMapEn)[0],
                'category_section_two' => $categoryMapEn['world'] ?? array_values($categoryMapEn)[0],
                'category_section_three' => $categoryMapEn['politics'] ?? array_values($categoryMapEn)[0],
                'category_section_four' => $categoryMapEn['sports'] ?? array_values($categoryMapEn)[0],
            ]
        );

        // Bangla
        HomeSectionSetting::updateOrCreate(
            ['language' => 'bn'],
            [
                'category_section_one' => $categoryMapBn['bangladesh'] ?? array_values($categoryMapBn)[0],
                'category_section_two' => $categoryMapBn['world'] ?? array_values($categoryMapBn)[0],
                'category_section_three' => $categoryMapBn['politics'] ?? array_values($categoryMapBn)[0],
                'category_section_four' => $categoryMapBn['sports'] ?? array_values($categoryMapBn)[0],
            ]
        );
    }

    private function seedStaticPages(): void
    {
        // English
        About::updateOrCreate(
            ['language' => 'en'],
            [
                'content' => 'United News of Bangladesh (UNB) is a leading news portal providing comprehensive coverage of national and international news, politics, business, sports, technology, entertainment, and more. We are committed to delivering accurate, timely, and unbiased news to our readers.',
            ]
        );

        Contact::updateOrCreate(
            ['language' => 'en'],
            [
                'address' => 'Cosmos Centre 69/1 New Circular Road, Malibagh, Dhaka-1217, Bangladesh',
                'phone' => '+880-2-933-1234',
                'email' => 'info@unb.com.bd',
            ]
        );

        // Bangla
        About::updateOrCreate(
            ['language' => 'bn'],
            [
                'content' => 'ইউনাইটেড নিউজ অব বাংলাদেশ (ইউএনবি) একটি শীর্ষস্থানীয় সংবাদ পোর্টাল যা জাতীয় ও আন্তর্জাতিক সংবাদ, রাজনীতি, ব্যবসা, খেলাধুলা, প্রযুক্তি, বিনোদন এবং আরও অনেক কিছু সম্পর্কে বিস্তৃত কভারেজ প্রদান করে। আমরা আমাদের পাঠকদের কাছে সঠিক, সময়োপযোগী এবং নিরপেক্ষ সংবাদ প্রদানের জন্য প্রতিশ্রুতিবদ্ধ।',
            ]
        );

        Contact::updateOrCreate(
            ['language' => 'bn'],
            [
                'address' => 'কসমস সেন্টার ৬৯/১ নিউ সার্কুলার রোড, মালিবাগ, ঢাকা-১২১৭, বাংলাদেশ',
                'phone' => '+880-2-933-1234',
                'email' => 'info@unb.com.bd',
            ]
        );
    }

    private function seedFooter(): void
    {
        // English
        FooterInfo::updateOrCreate(
            ['language' => 'en'],
            [
                'logo' => 'frontend/assets/images/logo1.png',
                'description' => 'United News of Bangladesh - Your trusted source for latest news, breaking news, and in-depth analysis from Bangladesh and around the world.',
                'copyright' => '© ' . now()->year . ' United News of Bangladesh. All rights reserved.',
            ]
        );

        $titlesEn = [
            'grid_one_title' => 'Resources',
            'grid_two_title' => 'Company',
            'grid_three_title' => 'Support',
        ];

        foreach ($titlesEn as $key => $value) {
            FooterTitle::updateOrCreate(
                ['key' => $key, 'language' => 'en'],
                ['value' => $value]
            );
        }

        $gridOneLinksEn = [
            ['name' => 'About', 'url' => '#'],
            ['name' => 'Privacy Policy', 'url' => '#'],
            ['name' => 'Advertisement', 'url' => '#'],
        ];

        FooterGridOne::where('language', 'en')->delete();
        foreach ($gridOneLinksEn as $link) {
            FooterGridOne::create(array_merge($link, ['language' => 'en', 'status' => true]));
        }

        $gridTwoLinksEn = [
            ['name' => 'Contact Us', 'url' => '#'],
            ['name' => 'Careers', 'url' => '#'],
            ['name' => 'Press Releases', 'url' => '#'],
        ];

        FooterGridTwo::where('language', 'en')->delete();
        foreach ($gridTwoLinksEn as $link) {
            FooterGridTwo::create(array_merge($link, ['language' => 'en', 'status' => true]));
        }

        $gridThreeLinksEn = [
            ['name' => 'Help Center', 'url' => '#'],
            ['name' => 'Terms of Use', 'url' => '#'],
            ['name' => 'Fact-Checking', 'url' => '#'],
        ];

        FooterGridThree::where('language', 'en')->delete();
        foreach ($gridThreeLinksEn as $link) {
            FooterGridThree::create(array_merge($link, ['language' => 'en', 'status' => true]));
        }

        // Bangla
        FooterInfo::updateOrCreate(
            ['language' => 'bn'],
            [
                'logo' => 'frontend/assets/images/logo1.png',
                'description' => 'ইউনাইটেড নিউজ অব বাংলাদেশ - বাংলাদেশ এবং বিশ্বজুড়ে সর্বশেষ সংবাদ, ব্রেকিং নিউজ এবং গভীর বিশ্লেষণের জন্য আপনার বিশ্বস্ত উৎস।',
                'copyright' => '© ' . now()->year . ' ইউনাইটেড নিউজ অব বাংলাদেশ। সর্বস্বত্ব সংরক্ষিত।',
            ]
        );

        $titlesBn = [
            'grid_one_title' => 'সম্পদ',
            'grid_two_title' => 'কোম্পানি',
            'grid_three_title' => 'সহায়তা',
        ];

        foreach ($titlesBn as $key => $value) {
            FooterTitle::updateOrCreate(
                ['key' => $key, 'language' => 'bn'],
                ['value' => $value]
            );
        }

        $gridOneLinksBn = [
            ['name' => 'আমাদের সম্পর্কে', 'url' => '#'],
            ['name' => 'গোপনীয়তা নীতি', 'url' => '#'],
            ['name' => 'বিজ্ঞাপন', 'url' => '#'],
        ];

        FooterGridOne::where('language', 'bn')->delete();
        foreach ($gridOneLinksBn as $link) {
            FooterGridOne::create(array_merge($link, ['language' => 'bn', 'status' => true]));
        }

        $gridTwoLinksBn = [
            ['name' => 'যোগাযোগ', 'url' => '#'],
            ['name' => 'ক্যারিয়ার', 'url' => '#'],
            ['name' => 'প্রেস রিলিজ', 'url' => '#'],
        ];

        FooterGridTwo::where('language', 'bn')->delete();
        foreach ($gridTwoLinksBn as $link) {
            FooterGridTwo::create(array_merge($link, ['language' => 'bn', 'status' => true]));
        }

        $gridThreeLinksBn = [
            ['name' => 'সহায়তা কেন্দ্র', 'url' => '#'],
            ['name' => 'ব্যবহারের শর্তাবলী', 'url' => '#'],
            ['name' => 'ফ্যাক্ট-চেক', 'url' => '#'],
        ];

        FooterGridThree::where('language', 'bn')->delete();
        foreach ($gridThreeLinksBn as $link) {
            FooterGridThree::create(array_merge($link, ['language' => 'bn', 'status' => true]));
        }
    }

    private function seedSocialWidgets(): void
    {
        $socialLinks = [
            ['icon' => 'fab fa-facebook-f', 'url' => 'https://facebook.com/unbnews', 'status' => true],
            ['icon' => 'fab fa-twitter', 'url' => 'https://twitter.com/unbnews', 'status' => true],
            ['icon' => 'fab fa-youtube', 'url' => 'https://youtube.com/unbnews', 'status' => true],
        ];

        SocialLink::query()->delete();
        foreach ($socialLinks as $link) {
            SocialLink::create($link);
        }

        // English
        $socialCountsEn = [
            [
                'icon' => 'fab fa-facebook-f',
                'url' => 'https://facebook.com/unbnews',
                'fan_count' => '2.5M',
                'fan_type' => 'Followers',
                'button_text' => 'Like',
                'color' => '#3b5998',
            ],
            [
                'icon' => 'fab fa-twitter',
                'url' => 'https://twitter.com/unbnews',
                'fan_count' => '1.8M',
                'fan_type' => 'Followers',
                'button_text' => 'Follow',
                'color' => '#1da1f2',
            ],
            [
                'icon' => 'fab fa-youtube',
                'url' => 'https://youtube.com/unbnews',
                'fan_count' => '3.2M',
                'fan_type' => 'Subscribers',
                'button_text' => 'Subscribe',
                'color' => '#ff0000',
            ],
        ];

        SocialCount::where('language', 'en')->delete();
        foreach ($socialCountsEn as $count) {
            SocialCount::create(array_merge($count, ['language' => 'en', 'status' => true]));
        }

        // Bangla
        $socialCountsBn = [
            [
                'icon' => 'fab fa-facebook-f',
                'url' => 'https://facebook.com/unbnews',
                'fan_count' => '২.৫ মিলিয়ন',
                'fan_type' => 'অনুসারী',
                'button_text' => 'লাইক',
                'color' => '#3b5998',
            ],
            [
                'icon' => 'fab fa-twitter',
                'url' => 'https://twitter.com/unbnews',
                'fan_count' => '১.৮ মিলিয়ন',
                'fan_type' => 'অনুসারী',
                'button_text' => 'ফলো',
                'color' => '#1da1f2',
            ],
            [
                'icon' => 'fab fa-youtube',
                'url' => 'https://youtube.com/unbnews',
                'fan_count' => '৩.২ মিলিয়ন',
                'fan_type' => 'সাবস্ক্রাইবার',
                'button_text' => 'সাবস্ক্রাইব',
                'color' => '#ff0000',
            ],
        ];

        SocialCount::where('language', 'bn')->delete();
        foreach ($socialCountsBn as $count) {
            SocialCount::create(array_merge($count, ['language' => 'bn', 'status' => true]));
        }
    }

    private function seedAds(): void
    {
        $placeholder = 'frontend/assets/images/placeholder.webp';
        Ad::updateOrCreate(
            ['id' => 1],
            [
                'home_top_bar_ad' => $placeholder,
                'home_top_bar_ad_status' => true,
                'home_top_bar_ad_url' => '#',
                'home_middle_ad' => $placeholder,
                'home_middle_ad_status' => true,
                'home_middle_ad_url' => '#',
                'view_page_ad' => $placeholder,
                'view_page_ad_status' => true,
                'view_page_ad_url' => '#',
                'news_page_ad' => $placeholder,
                'news_page_ad_status' => true,
                'news_page_ad_url' => '#',
                'side_bar_ad' => $placeholder,
                'side_bar_ad_status' => true,
                'side_bar_ad_url' => '#',
            ]
        );
    }

    private function seedArticles(array $categoryMap, array $tagMap, Admin $admin, string $language): void
    {
        $placeholder = 'frontend/assets/images/placeholder.webp';

        if ($language === 'en') {
            $articles = [
                [
                    'title' => 'Aftershocks continue as 3rd quake jolts Narsingdi, adjacent districts',
                    'category' => 'bangladesh',
                    'is_breaking_news' => true,
                    'show_at_slider' => true,
                    'show_at_popular' => true,
                    'tags' => ['Earthquake', 'Bangladesh', 'Breaking News'],
                    'summary' => 'A third earthquake has jolted Narsingdi and adjacent districts, continuing the series of aftershocks that have been affecting the region.',
                ],
                [
                    'title' => 'Bangladesh, Bhutan sign 2 MoUs on internet connectivity, health cooperation',
                    'category' => 'world',
                    'is_breaking_news' => false,
                    'show_at_slider' => true,
                    'show_at_popular' => true,
                    'tags' => ['International', 'Politics', 'Bangladesh'],
                    'summary' => 'Bangladesh and Bhutan have signed two Memorandums of Understanding focusing on internet connectivity and health cooperation, strengthening bilateral ties.',
                ],
                [
                    'title' => 'India should respect Bangladesh\'s legal systems, return Hasina: BIPSS President',
                    'category' => 'politics',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['Politics', 'International', 'Bangladesh'],
                    'summary' => 'The President of BIPSS has called on India to respect Bangladesh\'s legal systems and return Hasina, emphasizing the importance of respecting sovereignty.',
                ],
                [
                    'title' => 'Fakhrul warns Jamaat against \'misleading people\' by linking votes with \'Jannat\'',
                    'category' => 'politics',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['Politics', 'Election', 'Bangladesh'],
                    'summary' => 'BNP Secretary General Fakhrul has warned Jamaat-e-Islami against misleading people by linking votes with religious promises.',
                ],
                [
                    'title' => 'Gazipur Agricultural University to receive major upgrade worth Tk 567cr',
                    'category' => 'bangladesh',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['Education', 'Bangladesh', 'Local'],
                    'summary' => 'Gazipur Agricultural University is set to receive a major infrastructure upgrade worth Tk 567 crore, enhancing educational facilities.',
                ],
                [
                    'title' => 'Youth \'commits suicide\' in Dhaka',
                    'category' => 'bangladesh',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => false,
                    'tags' => ['Dhaka', 'Local', 'Bangladesh'],
                    'summary' => 'A young person has reportedly committed suicide in Dhaka, with authorities investigating the circumstances surrounding the incident.',
                ],
                [
                    'title' => 'Young adults hit hard as Bangladesh logs 593 dengue cases, 3 deaths',
                    'category' => 'bangladesh',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['Health', 'Bangladesh', 'Local'],
                    'summary' => 'Bangladesh has recorded 593 new dengue cases and 3 deaths, with young adults being particularly affected by the disease.',
                ],
                [
                    'title' => 'Bangladesh close in on victory as Taijul becomes country\'s leading Test wicket-taker',
                    'category' => 'sports',
                    'is_breaking_news' => false,
                    'show_at_slider' => true,
                    'show_at_popular' => true,
                    'tags' => ['Cricket', 'Sports', 'Bangladesh'],
                    'summary' => 'Bangladesh is closing in on victory in the Test match as Taijul Islam becomes the country\'s leading Test wicket-taker, achieving a historic milestone.',
                ],
                [
                    'title' => 'France to probe Musk\'s Grok chatbot following Holocaust denial allegations',
                    'category' => 'tech',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['Technology', 'International', 'Breaking News'],
                    'summary' => 'France has announced it will investigate Elon Musk\'s Grok chatbot following allegations of Holocaust denial, raising concerns about AI content moderation.',
                ],
                [
                    'title' => 'Ceiling And Wall Cracks After An Earthquake: When To Worry',
                    'category' => 'bangladesh',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['Earthquake', 'Health', 'Local'],
                    'summary' => 'Experts explain when to worry about ceiling and wall cracks after an earthquake, providing guidance for building safety assessment.',
                ],
            ];
        } else {
            // Bangla articles
            $articles = [
                [
                    'title' => 'ঢাকা ও আশপাশে শক্তিশালী ভূমিকম্প: আতঙ্কে ভবন ত্যাগ, গাজীপুরে শতাধিক শ্রমিক আহত',
                    'category' => 'bangladesh',
                    'is_breaking_news' => true,
                    'show_at_slider' => true,
                    'show_at_popular' => true,
                    'tags' => ['ভূমিকম্প', 'ঢাকা', 'ব্রেকিং নিউজ'],
                    'summary' => 'ঢাকা ও আশপাশের এলাকায় শক্তিশালী ভূমিকম্প আঘাত হেনেছে, যার ফলে আতঙ্কিত হয়ে মানুষ ভবন ত্যাগ করেছে এবং গাজীপুরে শতাধিক শ্রমিক আহত হয়েছে।',
                ],
                [
                    'title' => 'ভুটানের প্রধানমন্ত্রী শেরিং তোবগে ঢাকায়; রাষ্ট্রীয় সফরে পূর্ণ সম্মাননা',
                    'category' => 'world',
                    'is_breaking_news' => false,
                    'show_at_slider' => true,
                    'show_at_popular' => true,
                    'tags' => ['আন্তর্জাতিক', 'রাজনীতি', 'বাংলাদেশ'],
                    'summary' => 'ভুটানের প্রধানমন্ত্রী শেরিং তোবগে ঢাকায় এসেছেন রাষ্ট্রীয় সফরে, যেখানে তাকে পূর্ণ রাষ্ট্রীয় সম্মাননা প্রদান করা হয়েছে।',
                ],
                [
                    'title' => 'আরমানিটোলায় ভবনধসের পর রাজউকের পরিদর্শন ও নতুন সিদ্ধান্ত',
                    'category' => 'bangladesh',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['ঢাকা', 'স্থানীয়', 'বাংলাদেশ'],
                    'summary' => 'আরমানিটোলায় ভবনধসের পর রাজউক কর্তৃপক্ষ পরিদর্শন করেছে এবং নতুন সিদ্ধান্ত গ্রহণ করেছে ভবন নিরাপত্তা নিশ্চিত করার জন্য।',
                ],
                [
                    'title' => 'শীতের সবজির সরবরাহ বেড়েছে মানিকগঞ্জে; ভাল দাম পাচ্ছেন কৃষক',
                    'category' => 'bangladesh',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['স্থানীয়', 'ব্যবসা', 'বাংলাদেশ'],
                    'summary' => 'মানিকগঞ্জে শীতের সবজির সরবরাহ বেড়েছে এবং কৃষকরা ভাল দাম পাচ্ছেন, যা স্থানীয় কৃষি অর্থনীতির জন্য ইতিবাচক সংকেত।',
                ],
                [
                    'title' => 'সশস্ত্র বাহিনী দিবসে প্রধান উপদেষ্টার বৈষম্যহীন ও জনকল্যাণমূলক রাষ্ট্র গড়ার প্রতিশ্রুতি',
                    'category' => 'politics',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['রাজনীতি', 'বাংলাদেশ'],
                    'summary' => 'সশস্ত্র বাহিনী দিবসে প্রধান উপদেষ্টা বৈষম্যহীন ও জনকল্যাণমূলক রাষ্ট্র গড়ার প্রতিশ্রুতি দিয়েছেন।',
                ],
                [
                    'title' => 'সিসিটিভির ফুটেজে ঢাকার ভূমিকম্পের ভয়াবহতা',
                    'category' => 'bangladesh',
                    'is_breaking_news' => false,
                    'show_at_slider' => false,
                    'show_at_popular' => true,
                    'tags' => ['ভূমিকম্প', 'ঢাকা', 'ব্রেকিং নিউজ'],
                    'summary' => 'সিসিটিভির ফুটেজে ঢাকায় ভূমিকম্পের ভয়াবহতা ধরা পড়েছে, যা ভবনগুলোর অবস্থা এবং মানুষের প্রতিক্রিয়া দেখায়।',
                ],
            ];
        }

        foreach ($articles as $index => $article) {
            $slug = Str::slug($article['title']);
            $news = News::updateOrCreate(
                ['slug' => $slug, 'language' => $language],
                [
                    'language' => $language,
                    'category_id' => $categoryMap[$article['category']] ?? array_values($categoryMap)[0],
                    'auther_id' => $admin->id,
                    'image' => $placeholder,
                    'title' => $article['title'],
                    'content' => $article['summary'] . ' ' . ($language === 'en' 
                        ? 'This is a comprehensive report covering all aspects of the story, including expert opinions, official statements, and detailed analysis of the situation. The report provides readers with a complete understanding of the events and their implications.'
                        : 'এটি একটি বিস্তৃত প্রতিবেদন যা গল্পের সমস্ত দিক কভার করে, বিশেষজ্ঞ মতামত, সরকারী বিবৃতি এবং পরিস্থিতির বিশদ বিশ্লেষণ সহ। প্রতিবেদনটি পাঠকদের ঘটনাগুলি এবং তাদের প্রভাব সম্পর্কে সম্পূর্ণ বোঝাপড়া প্রদান করে।'),
                    'meta_title' => $article['title'],
                    'meta_description' => $article['summary'],
                    'is_breaking_news' => $article['is_breaking_news'],
                    'show_at_slider' => $article['show_at_slider'],
                    'show_at_popular' => $article['show_at_popular'],
                    'is_approved' => true,
                    'status' => true,
                    'views' => 250 + ($index * 75),
                    'created_by' => $admin->id,
                    'created_by_type' => 'admin',
                    'updated_by' => $admin->id,
                    'updated_by_type' => 'admin',
                    'approve_by' => $admin->id,
                    'approve_by_type' => 'admin',
                ]
            );

            $tagIds = collect($article['tags'])
                ->map(fn ($tag) => $tagMap[$tag] ?? null)
                ->filter()
                ->values()
                ->all();

            if (!empty($tagIds)) {
                $news->tags()->sync($tagIds);
            }
        }
    }
}
