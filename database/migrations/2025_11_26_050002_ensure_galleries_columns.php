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
        // Use raw SQL to ensure columns are added
        $columns = DB::select('SHOW COLUMNS FROM galleries');
        $existingColumns = array_column($columns, 'Field');
        
        $columnsToAdd = [];
        
        if (!in_array('type', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN type ENUM('image', 'video') DEFAULT 'image' AFTER id";
        }
        if (!in_array('title', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN title VARCHAR(255) NULL";
        }
        if (!in_array('description', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN description TEXT NULL";
        }
        if (!in_array('alt_text', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN alt_text VARCHAR(255) NULL";
        }
        if (!in_array('caption', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN caption VARCHAR(255) NULL";
        }
        if (!in_array('media_id', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN media_id BIGINT UNSIGNED NULL";
        }
        if (!in_array('video_url', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN video_url TEXT NULL";
        }
        if (!in_array('video_platform', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN video_platform VARCHAR(255) NULL";
        }
        if (!in_array('video_id', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN video_id VARCHAR(255) NULL";
        }
        if (!in_array('gallery_slug', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN gallery_slug VARCHAR(255) NULL";
        }
        if (!in_array('sort_order', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN sort_order INT DEFAULT 0";
        }
        if (!in_array('is_exclusive', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN is_exclusive TINYINT(1) DEFAULT 0";
        }
        if (!in_array('status', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN status TINYINT(1) DEFAULT 1";
        }
        if (!in_array('language', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN language VARCHAR(10) DEFAULT 'en'";
        }
        if (!in_array('created_by', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN created_by BIGINT UNSIGNED NULL";
        }
        if (!in_array('created_by_type', $existingColumns)) {
            $columnsToAdd[] = "ADD COLUMN created_by_type VARCHAR(255) DEFAULT 'App\\\\Models\\\\Admin'";
        }
        
        if (!empty($columnsToAdd)) {
            DB::statement("ALTER TABLE galleries " . implode(', ', $columnsToAdd));
        }
        
        // Add indexes if they don't exist
        try {
            DB::statement("CREATE INDEX IF NOT EXISTS galleries_gallery_slug_index ON galleries(gallery_slug)");
        } catch (\Exception $e) {
            // Index might already exist, try without IF NOT EXISTS
            try {
                DB::statement("ALTER TABLE galleries ADD INDEX galleries_gallery_slug_index (gallery_slug)");
            } catch (\Exception $e2) {
                // Ignore if already exists
            }
        }
        
        try {
            DB::statement("ALTER TABLE galleries ADD INDEX galleries_type_status_index (type, status)");
        } catch (\Exception $e) {
            // Ignore if already exists
        }
        
        try {
            DB::statement("ALTER TABLE galleries ADD INDEX galleries_gallery_slug_sort_order_index (gallery_slug, sort_order)");
        } catch (\Exception $e) {
            // Ignore if already exists
        }
        
        try {
            DB::statement("ALTER TABLE galleries ADD INDEX galleries_language_index (language)");
        } catch (\Exception $e) {
            // Ignore if already exists
        }
        
        // Add foreign key
        if (Schema::hasTable('media')) {
            try {
                DB::statement("ALTER TABLE galleries ADD CONSTRAINT galleries_media_id_foreign FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE SET NULL");
            } catch (\Exception $e) {
                // Foreign key might already exist
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop columns in down() to avoid data loss
    }
};

