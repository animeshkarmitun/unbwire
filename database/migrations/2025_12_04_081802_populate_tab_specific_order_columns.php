<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all languages
        $languages = DB::table('languages')->pluck('lang');
        
        foreach ($languages as $language) {
            // Populate breaking_order for breaking news (per language)
            $breakingNews = DB::table('news')
                ->where('is_breaking_news', 1)
                ->where('language', $language)
                ->orderBy('order_position', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->get();

            foreach ($breakingNews as $index => $news) {
                DB::table('news')
                    ->where('id', $news->id)
                    ->update(['breaking_order' => $index + 1]);
            }

            // Populate slider_order for slider news (per language)
            $sliderNews = DB::table('news')
                ->where('show_at_slider', 1)
                ->where('language', $language)
                ->orderBy('order_position', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->get();

            foreach ($sliderNews as $index => $news) {
                DB::table('news')
                    ->where('id', $news->id)
                    ->update(['slider_order' => $index + 1]);
            }

            // Populate popular_order for popular news (per language)
            $popularNews = DB::table('news')
                ->where('show_at_popular', 1)
                ->where('language', $language)
                ->orderBy('order_position', 'ASC')
                ->orderBy('created_at', 'DESC')
                ->get();

            foreach ($popularNews as $index => $news) {
                DB::table('news')
                    ->where('id', $news->id)
                    ->update(['popular_order' => $index + 1]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reset all order columns to 0
        DB::table('news')->update([
            'breaking_order' => 0,
            'slider_order' => 0,
            'popular_order' => 0
        ]);
    }
};
