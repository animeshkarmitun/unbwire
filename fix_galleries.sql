-- Fix galleries table by adding all missing columns
-- Run this SQL directly in your database (phpMyAdmin, MySQL Workbench, or command line)

ALTER TABLE galleries 
ADD COLUMN IF NOT EXISTS type ENUM('image', 'video') DEFAULT 'image' AFTER id,
ADD COLUMN IF NOT EXISTS title VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS description TEXT NULL,
ADD COLUMN IF NOT EXISTS alt_text VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS caption VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS media_id BIGINT UNSIGNED NULL,
ADD COLUMN IF NOT EXISTS video_url TEXT NULL,
ADD COLUMN IF NOT EXISTS video_platform VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS video_id VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS gallery_slug VARCHAR(255) NULL,
ADD COLUMN IF NOT EXISTS sort_order INT DEFAULT 0,
ADD COLUMN IF NOT EXISTS is_exclusive TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS status TINYINT(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS language VARCHAR(10) DEFAULT 'en',
ADD COLUMN IF NOT EXISTS created_by BIGINT UNSIGNED NULL,
ADD COLUMN IF NOT EXISTS created_by_type VARCHAR(255) DEFAULT 'App\\Models\\Admin';

-- Add indexes
CREATE INDEX IF NOT EXISTS galleries_gallery_slug_index ON galleries(gallery_slug);
CREATE INDEX IF NOT EXISTS galleries_type_status_index ON galleries(type, status);
CREATE INDEX IF NOT EXISTS galleries_gallery_slug_sort_order_index ON galleries(gallery_slug, sort_order);
CREATE INDEX IF NOT EXISTS galleries_language_index ON galleries(language);

-- Add foreign key (if media table exists)
ALTER TABLE galleries 
ADD CONSTRAINT galleries_media_id_foreign 
FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE SET NULL;

