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
        Schema::create('subscription_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // UNB Lite, UNB Pro, UNB Ultra
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('billing_period', ['monthly', 'yearly'])->default('monthly');
            
            // Access permissions
            $table->boolean('access_news')->default(true);
            $table->boolean('access_images')->default(false);
            $table->boolean('access_videos')->default(false);
            $table->boolean('access_exclusive')->default(false);
            
            // Features
            $table->integer('max_articles_per_day')->nullable(); // null = unlimited
            $table->boolean('ad_free')->default(false);
            $table->boolean('priority_support')->default(false);
            
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_packages');
    }
};

