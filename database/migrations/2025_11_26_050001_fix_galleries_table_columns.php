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
        // Check if galleries table exists and has only basic columns
        if (Schema::hasTable('galleries')) {
            $columns = Schema::getColumnListing('galleries');
            
            // If table only has id and timestamps, add all columns
            if (count($columns) <= 3) {
                DB::statement("ALTER TABLE galleries 
                    ADD COLUMN type ENUM('image', 'video') DEFAULT 'image' AFTER id,
                    ADD COLUMN title VARCHAR(255) NULL AFTER type,
                    ADD COLUMN description TEXT NULL AFTER title,
                    ADD COLUMN alt_text VARCHAR(255) NULL AFTER description,
                    ADD COLUMN caption VARCHAR(255) NULL AFTER alt_text,
                    ADD COLUMN media_id BIGINT UNSIGNED NULL AFTER caption,
                    ADD COLUMN video_url TEXT NULL AFTER media_id,
                    ADD COLUMN video_platform VARCHAR(255) NULL AFTER video_url,
                    ADD COLUMN video_id VARCHAR(255) NULL AFTER video_platform,
                    ADD COLUMN gallery_slug VARCHAR(255) NULL AFTER video_id,
                    ADD COLUMN sort_order INT DEFAULT 0 AFTER gallery_slug,
                    ADD COLUMN is_exclusive TINYINT(1) DEFAULT 0 AFTER sort_order,
                    ADD COLUMN status TINYINT(1) DEFAULT 1 AFTER is_exclusive,
                    ADD COLUMN language VARCHAR(10) DEFAULT 'en' AFTER status,
                    ADD COLUMN created_by BIGINT UNSIGNED NULL AFTER language,
                    ADD COLUMN created_by_type VARCHAR(255) DEFAULT 'App\\\\Models\\\\Admin' AFTER created_by
                ");
                
                // Add indexes
                DB::statement("ALTER TABLE galleries ADD INDEX galleries_gallery_slug_index (gallery_slug)");
                DB::statement("ALTER TABLE galleries ADD INDEX galleries_type_status_index (type, status)");
                DB::statement("ALTER TABLE galleries ADD INDEX galleries_gallery_slug_sort_order_index (gallery_slug, sort_order)");
                DB::statement("ALTER TABLE galleries ADD INDEX galleries_language_index (language)");
                
                // Add foreign key if media table exists
                if (Schema::hasTable('media')) {
                    try {
                        DB::statement("ALTER TABLE galleries ADD CONSTRAINT galleries_media_id_foreign FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE SET NULL");
                    } catch (\Exception $e) {
                        // Foreign key might already exist
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('galleries')) {
            try {
                DB::statement("ALTER TABLE galleries DROP FOREIGN KEY galleries_media_id_foreign");
            } catch (\Exception $e) {
                // Ignore if foreign key doesn't exist
            }
            
            DB::statement("ALTER TABLE galleries 
                DROP COLUMN type,
                DROP COLUMN title,
                DROP COLUMN description,
                DROP COLUMN alt_text,
                DROP COLUMN caption,
                DROP COLUMN media_id,
                DROP COLUMN video_url,
                DROP COLUMN video_platform,
                DROP COLUMN video_id,
                DROP COLUMN gallery_slug,
                DROP COLUMN sort_order,
                DROP COLUMN is_exclusive,
                DROP COLUMN status,
                DROP COLUMN language,
                DROP COLUMN created_by,
                DROP COLUMN created_by_type
            ");
        }
    }
};

