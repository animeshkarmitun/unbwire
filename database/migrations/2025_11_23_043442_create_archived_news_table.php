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
        Schema::create('archived_news', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_id')->nullable(); // Original news ID for reference
            $table->string('language');
            $table->unsignedBigInteger('category_id')->nullable(); // Keep nullable in case category is deleted
            $table->unsignedBigInteger('auther_id')->nullable(); // Keep nullable in case admin is deleted
            $table->text('image');
            $table->string('title');
            $table->text('slug');
            $table->text('content');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->boolean('is_breaking_news')->default(0);
            $table->boolean('show_at_slider')->default(0);
            $table->boolean('show_at_popular')->default(0);
            $table->boolean('is_approved')->default(0);
            $table->boolean('status')->default(0);
            $table->integer('views')->default(0);
            
            // Subscription fields
            $table->boolean('is_exclusive')->default(false);
            $table->string('video_url')->nullable();
            $table->enum('subscription_required', ['free', 'lite', 'pro', 'ultra'])->default('free');
            
            // Archive metadata
            $table->unsignedBigInteger('deleted_by')->nullable(); // Admin who deleted it
            $table->timestamp('deleted_at');
            $table->text('deletion_reason')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('original_id');
            $table->index('deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archived_news');
    }
};
