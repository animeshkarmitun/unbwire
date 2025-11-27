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
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['image', 'video'])->default('image');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            
            // Media library reference (for uploaded images/videos)
            $table->unsignedBigInteger('media_id')->nullable();
            
            // External video URL (for YouTube, Facebook, Vimeo, etc.)
            $table->text('video_url')->nullable();
            $table->string('video_platform')->nullable(); // youtube, facebook, vimeo, etc.
            $table->string('video_id')->nullable(); // Extracted video ID from URL
            
            // Gallery grouping (for multiple items in same gallery)
            $table->string('gallery_slug')->nullable()->index();
            $table->integer('sort_order')->default(0);
            
            // Status and exclusivity
            $table->boolean('is_exclusive')->default(false);
            $table->boolean('status')->default(true);
            
            // Language support
            $table->string('language')->default('en');
            
            // Created by
            $table->unsignedBigInteger('created_by')->nullable();
            $table->string('created_by_type')->default('App\Models\Admin');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'status']);
            $table->index(['gallery_slug', 'sort_order']);
            $table->index('language');
            $table->foreign('media_id')->references('id')->on('media')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galleries');
    }
};
