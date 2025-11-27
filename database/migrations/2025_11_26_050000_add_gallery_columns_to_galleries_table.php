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
        Schema::table('galleries', function (Blueprint $table) {
            // Add type column
            if (!Schema::hasColumn('galleries', 'type')) {
                $table->enum('type', ['image', 'video'])->default('image')->after('id');
            }
            
            // Add metadata columns
            if (!Schema::hasColumn('galleries', 'title')) {
                $table->string('title')->nullable()->after('type');
            }
            if (!Schema::hasColumn('galleries', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            if (!Schema::hasColumn('galleries', 'alt_text')) {
                $table->string('alt_text')->nullable()->after('description');
            }
            if (!Schema::hasColumn('galleries', 'caption')) {
                $table->string('caption')->nullable()->after('alt_text');
            }
            
            // Media library reference
            if (!Schema::hasColumn('galleries', 'media_id')) {
                $table->unsignedBigInteger('media_id')->nullable()->after('caption');
            }
            
            // External video URL columns
            if (!Schema::hasColumn('galleries', 'video_url')) {
                $table->text('video_url')->nullable()->after('media_id');
            }
            if (!Schema::hasColumn('galleries', 'video_platform')) {
                $table->string('video_platform')->nullable()->after('video_url');
            }
            if (!Schema::hasColumn('galleries', 'video_id')) {
                $table->string('video_id')->nullable()->after('video_platform');
            }
            
            // Gallery grouping
            if (!Schema::hasColumn('galleries', 'gallery_slug')) {
                $table->string('gallery_slug')->nullable()->after('video_id');
            }
            if (!Schema::hasColumn('galleries', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('gallery_slug');
            }
            
            // Status and exclusivity
            if (!Schema::hasColumn('galleries', 'is_exclusive')) {
                $table->boolean('is_exclusive')->default(false)->after('sort_order');
            }
            if (!Schema::hasColumn('galleries', 'status')) {
                $table->boolean('status')->default(true)->after('is_exclusive');
            }
            
            // Language support
            if (!Schema::hasColumn('galleries', 'language')) {
                $table->string('language')->default('en')->after('status');
            }
            
            // Created by
            if (!Schema::hasColumn('galleries', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('language');
            }
            if (!Schema::hasColumn('galleries', 'created_by_type')) {
                $table->string('created_by_type')->default('App\Models\Admin')->after('created_by');
            }
        });

        // Add indexes (using try-catch to handle if they already exist)
        try {
            Schema::table('galleries', function (Blueprint $table) {
                $table->index('gallery_slug');
                $table->index(['type', 'status']);
                $table->index(['gallery_slug', 'sort_order']);
                $table->index('language');
            });
        } catch (\Exception $e) {
            // Indexes might already exist, ignore
        }

        // Add foreign key
        try {
            if (Schema::hasTable('media')) {
                Schema::table('galleries', function (Blueprint $table) {
                    $table->foreign('media_id')->references('id')->on('media')->onDelete('set null');
                });
            }
        } catch (\Exception $e) {
            // Foreign key might already exist, ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['media_id']);
            
            // Drop indexes
            $table->dropIndex(['gallery_slug']);
            $table->dropIndex(['type', 'status']);
            $table->dropIndex(['gallery_slug', 'sort_order']);
            $table->dropIndex(['language']);
            
            // Drop columns
            $table->dropColumn([
                'type',
                'title',
                'description',
                'alt_text',
                'caption',
                'media_id',
                'video_url',
                'video_platform',
                'video_id',
                'gallery_slug',
                'sort_order',
                'is_exclusive',
                'status',
                'language',
                'created_by',
                'created_by_type',
            ]);
        });
    }
};

