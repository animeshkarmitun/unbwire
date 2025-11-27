-- Simple SQL to fix galleries table (MySQL 5.7+ compatible)
-- Run this in your MySQL client

-- Check and add columns one by one (MySQL doesn't support IF NOT EXISTS in ALTER TABLE)
ALTER TABLE galleries ADD COLUMN type ENUM('image', 'video') DEFAULT 'image' AFTER id;
ALTER TABLE galleries ADD COLUMN title VARCHAR(255) NULL;
ALTER TABLE galleries ADD COLUMN description TEXT NULL;
ALTER TABLE galleries ADD COLUMN alt_text VARCHAR(255) NULL;
ALTER TABLE galleries ADD COLUMN caption VARCHAR(255) NULL;
ALTER TABLE galleries ADD COLUMN media_id BIGINT UNSIGNED NULL;
ALTER TABLE galleries ADD COLUMN video_url TEXT NULL;
ALTER TABLE galleries ADD COLUMN video_platform VARCHAR(255) NULL;
ALTER TABLE galleries ADD COLUMN video_id VARCHAR(255) NULL;
ALTER TABLE galleries ADD COLUMN gallery_slug VARCHAR(255) NULL;
ALTER TABLE galleries ADD COLUMN sort_order INT DEFAULT 0;
ALTER TABLE galleries ADD COLUMN is_exclusive TINYINT(1) DEFAULT 0;
ALTER TABLE galleries ADD COLUMN status TINYINT(1) DEFAULT 1;
ALTER TABLE galleries ADD COLUMN language VARCHAR(10) DEFAULT 'en';
ALTER TABLE galleries ADD COLUMN created_by BIGINT UNSIGNED NULL;
ALTER TABLE galleries ADD COLUMN created_by_type VARCHAR(255) DEFAULT 'App\\Models\\Admin';

-- Add indexes
ALTER TABLE galleries ADD INDEX galleries_gallery_slug_index (gallery_slug);
ALTER TABLE galleries ADD INDEX galleries_type_status_index (type, status);
ALTER TABLE galleries ADD INDEX galleries_gallery_slug_sort_order_index (gallery_slug, sort_order);
ALTER TABLE galleries ADD INDEX galleries_language_index (language);

-- Add foreign key
ALTER TABLE galleries ADD CONSTRAINT galleries_media_id_foreign FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE SET NULL;

