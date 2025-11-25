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
        Schema::create('analytics_summary', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique()->index();
            $table->integer('visitors')->default(0);
            $table->integer('new_visitors')->default(0);
            $table->integer('returning_visitors')->default(0);
            $table->integer('visits')->default(0);
            $table->integer('page_views')->default(0);
            $table->integer('unique_page_views')->default(0);
            $table->decimal('bounce_rate', 5, 2)->nullable()->comment('Percentage');
            $table->integer('avg_session_duration')->nullable()->comment('Average in seconds');
            $table->integer('organic_traffic')->default(0);
            $table->integer('direct_traffic')->default(0);
            $table->integer('social_traffic')->default(0);
            $table->integer('referral_traffic')->default(0);
            $table->string('top_country', 100)->nullable();
            $table->string('top_referrer')->nullable();
            $table->timestamps();
            
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_summary');
    }
};
