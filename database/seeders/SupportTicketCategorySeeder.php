<?php

namespace Database\Seeders;

use App\Models\SupportTicketCategory;
use Illuminate\Database\Seeder;

class SupportTicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technical Support',
                'slug' => 'technical-support',
                'description' => 'Technical issues, bugs, and system problems',
                'icon' => 'fas fa-cog',
                'color' => '#3498db',
                'sla_hours' => 24,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Account Issues',
                'slug' => 'account-issues',
                'description' => 'Account access, password, profile issues',
                'icon' => 'fas fa-user',
                'color' => '#9b59b6',
                'sla_hours' => 12,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Subscription & Billing',
                'slug' => 'subscription-billing',
                'description' => 'Subscription questions, billing, payments',
                'icon' => 'fas fa-credit-card',
                'color' => '#2ecc71',
                'sla_hours' => 48,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Content & News',
                'slug' => 'content-news',
                'description' => 'Questions about news content, articles, media',
                'icon' => 'fas fa-newspaper',
                'color' => '#e74c3c',
                'sla_hours' => 72,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'General Inquiry',
                'slug' => 'general-inquiry',
                'description' => 'General questions and information requests',
                'icon' => 'fas fa-question-circle',
                'color' => '#f39c12',
                'sla_hours' => 48,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Feature Request',
                'slug' => 'feature-request',
                'description' => 'Suggestions for new features or improvements',
                'icon' => 'fas fa-lightbulb',
                'color' => '#1abc9c',
                'sla_hours' => null,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($categories as $category) {
            SupportTicketCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
