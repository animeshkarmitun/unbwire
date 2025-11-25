<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->boolean('is_exclusive')->default(false)->after('is_breaking_news');
            $table->string('video_url')->nullable()->after('image');
            $table->enum('subscription_required', ['free', 'lite', 'pro', 'ultra'])->default('free')->after('is_exclusive');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn(['is_exclusive', 'video_url', 'subscription_required']);
        });
    }
};

