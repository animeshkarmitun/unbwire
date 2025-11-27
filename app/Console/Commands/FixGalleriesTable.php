<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixGalleriesTable extends Command
{
    protected $signature = 'galleries:fix-table';
    protected $description = 'Fix galleries table by adding missing columns';

    public function handle()
    {
        $this->info('Checking galleries table structure...');

        try {
            $columns = DB::select('SHOW COLUMNS FROM galleries');
            $existingColumns = array_column($columns, 'Field');
            
            $this->info('Existing columns: ' . implode(', ', $existingColumns));
            
            if (!in_array('gallery_slug', $existingColumns)) {
                $this->warn('gallery_slug column is missing. Adding columns...');
                
                $alterStatements = [];
                
                if (!in_array('type', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN type ENUM('image', 'video') DEFAULT 'image' AFTER id";
                }
                if (!in_array('title', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN title VARCHAR(255) NULL";
                }
                if (!in_array('description', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN description TEXT NULL";
                }
                if (!in_array('alt_text', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN alt_text VARCHAR(255) NULL";
                }
                if (!in_array('caption', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN caption VARCHAR(255) NULL";
                }
                if (!in_array('media_id', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN media_id BIGINT UNSIGNED NULL";
                }
                if (!in_array('video_url', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN video_url TEXT NULL";
                }
                if (!in_array('video_platform', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN video_platform VARCHAR(255) NULL";
                }
                if (!in_array('video_id', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN video_id VARCHAR(255) NULL";
                }
                if (!in_array('gallery_slug', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN gallery_slug VARCHAR(255) NULL";
                }
                if (!in_array('sort_order', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN sort_order INT DEFAULT 0";
                }
                if (!in_array('is_exclusive', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN is_exclusive TINYINT(1) DEFAULT 0";
                }
                if (!in_array('status', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN status TINYINT(1) DEFAULT 1";
                }
                if (!in_array('language', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN language VARCHAR(10) DEFAULT 'en'";
                }
                if (!in_array('created_by', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN created_by BIGINT UNSIGNED NULL";
                }
                if (!in_array('created_by_type', $existingColumns)) {
                    $alterStatements[] = "ADD COLUMN created_by_type VARCHAR(255) DEFAULT 'App\\\\Models\\\\Admin'";
                }
                
                if (!empty($alterStatements)) {
                    $sql = "ALTER TABLE galleries " . implode(', ', $alterStatements);
                    $this->line('Executing SQL...');
                    DB::statement($sql);
                    $this->info('✓ Columns added successfully!');
                }
                
                // Add indexes
                try {
                    DB::statement("ALTER TABLE galleries ADD INDEX galleries_gallery_slug_index (gallery_slug)");
                    $this->info('✓ Index on gallery_slug added');
                } catch (\Exception $e) {
                    $this->comment('Index on gallery_slug might already exist');
                }
                
                try {
                    DB::statement("ALTER TABLE galleries ADD INDEX galleries_type_status_index (type, status)");
                    $this->info('✓ Index on type, status added');
                } catch (\Exception $e) {
                    $this->comment('Index on type, status might already exist');
                }
                
                // Add foreign key
                if (Schema::hasTable('media')) {
                    try {
                        DB::statement("ALTER TABLE galleries ADD CONSTRAINT galleries_media_id_foreign FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE SET NULL");
                        $this->info('✓ Foreign key added');
                    } catch (\Exception $e) {
                        $this->comment('Foreign key might already exist');
                    }
                }
                
            } else {
                $this->info('✓ gallery_slug column already exists!');
            }
            
            // Verify
            $columns = DB::select('SHOW COLUMNS FROM galleries');
            $this->info("\nFinal table structure (" . count($columns) . " columns):");
            foreach ($columns as $col) {
                $this->line("  - " . $col->Field . " (" . $col->Type . ")");
            }
            
            $this->info("\n✓ Done! Table structure is now correct.");
            return 0;
            
        } catch (\Exception $e) {
            $this->error('ERROR: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }
}

