<?php

namespace App\Services;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Exception;

class ImageProcessingService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Process image with cropping, WebP conversion, and watermark
     *
     * @param UploadedFile $file
     * @param array $cropData Crop coordinates: x, y, width, height, rotate
     * @param bool $convertToWebp
     * @param bool $addWatermark
     * @param string|null $watermarkPosition Position: center, top-left, top-center, top-right, middle-left, middle-right, bottom-left, bottom-center, bottom-right
     * @param string $storagePath Base storage path (e.g., 'uploads')
     * @param bool $keepOriginal
     * @return array Returns ['processed_path' => string, 'original_path' => string|null, 'webp_path' => string|null]
     */
    public function processImage(
        UploadedFile $file,
        ?array $cropData = null,
        bool $convertToWebp = true,
        bool $addWatermark = false,
        ?string $watermarkPosition = 'center',
        string $storagePath = 'uploads',
        bool $keepOriginal = true
    ): array {
        try {
            $originalExtension = $file->getClientOriginalExtension();
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $uniqueId = \Illuminate\Support\Str::random(40);

            $results = [
                'processed_path' => null,
                'original_path' => null,
                'webp_path' => null,
            ];

            // Read the image
            $image = $this->manager->read($file->getRealPath());

            // Apply cropping if provided
            if ($cropData && isset($cropData['x'], $cropData['y'], $cropData['width'], $cropData['height'])) {
                $x = (int) $cropData['x'];
                $y = (int) $cropData['y'];
                $width = (int) $cropData['width'];
                $height = (int) $cropData['height'];
                $rotate = isset($cropData['rotate']) ? (float) $cropData['rotate'] : 0;

                // Apply rotation if needed
                if ($rotate != 0) {
                    $image->rotate(-$rotate);
                }

                // Crop the image
                $image->crop($width, $height, $x, $y);
            }

            // Save original if requested
            if ($keepOriginal) {
                $originalFilenameFull = $uniqueId . '_original.' . $originalExtension;
                $originalPath = $storagePath . '/' . $originalFilenameFull;
                $image->save(storage_path('app/public/' . $originalPath), quality: 90);
                $results['original_path'] = 'storage/' . $originalPath;
            }

            // Add watermark if requested
            if ($addWatermark) {
                $image = $this->addWatermark($image, $watermarkPosition);
            }

            // Save processed image (original format)
            $processedFilename = $uniqueId . '.' . $originalExtension;
            $processedPath = $storagePath . '/' . $processedFilename;
            $image->save(storage_path('app/public/' . $processedPath), quality: 90);
            $results['processed_path'] = 'storage/' . $processedPath;

            // Convert to WebP if requested
            if ($convertToWebp) {
                $webpFilename = $uniqueId . '.webp';
                $webpPath = $storagePath . '/' . $webpFilename;
                $image->toWebp(90)->save(storage_path('app/public/' . $webpPath));
                $results['webp_path'] = 'storage/' . $webpPath;
            }

            return $results;
        } catch (Exception $e) {
            \Log::error('Image processing error: ' . $e->getMessage());
            throw new Exception('Failed to process image: ' . $e->getMessage());
        }
    }

    /**
     * Add watermark to image
     *
     * @param \Intervention\Image\Image $image
     * @param string $position
     * @return \Intervention\Image\Image
     */
    protected function addWatermark($image, string $position = 'center')
    {
        // Get watermark settings from database or config
        $watermarkSettings = $this->getWatermarkSettings();

        if (!$watermarkSettings || !$watermarkSettings['enabled']) {
            return $image;
        }

        // Load watermark image
        $watermarkPath = $watermarkSettings['watermark_image'] ?? null;
        if (!$watermarkPath || !file_exists(public_path($watermarkPath))) {
            \Log::warning('Watermark image not found: ' . $watermarkPath);
            return $image;
        }

        try {
            $watermark = $this->manager->read(public_path($watermarkPath));
            
            // Resize watermark if needed (optional - maintain aspect ratio)
            $watermarkSize = $watermarkSettings['size'] ?? 100; // percentage
            if ($watermarkSize < 100) {
                $maxWidth = ($image->width() * $watermarkSize) / 100;
                $maxHeight = ($image->height() * $watermarkSize) / 100;
                $watermark->scaleDown($maxWidth, $maxHeight);
            }

            // Get opacity setting (0-100)
            $opacity = $watermarkSettings['opacity'] ?? 100;
            // Ensure opacity is between 0 and 100
            $opacity = max(0, min(100, (int) $opacity));

            // Calculate position
            $positionData = $this->calculateWatermarkPosition(
                $image->width(),
                $image->height(),
                $watermark->width(),
                $watermark->height(),
                $position,
                $watermarkSettings['offset'] ?? 10
            );

            // Place watermark with opacity
            // In Intervention Image v3, opacity is passed as the 5th parameter to place()
            // place($element, $position, $offset_x, $offset_y, $opacity)
            $image->place(
                $watermark,
                'top-left', // Position anchor
                $positionData['x'], // X offset
                $positionData['y'], // Y offset
                $opacity // Opacity (0-100)
            );

            return $image;
        } catch (Exception $e) {
            \Log::error('Watermark error: ' . $e->getMessage());
            return $image; // Return original if watermark fails
        }
    }

    /**
     * Calculate watermark position coordinates
     *
     * @param int $imageWidth
     * @param int $imageHeight
     * @param int $watermarkWidth
     * @param int $watermarkHeight
     * @param string $position
     * @param int $offset
     * @return array
     */
    protected function calculateWatermarkPosition(
        int $imageWidth,
        int $imageHeight,
        int $watermarkWidth,
        int $watermarkHeight,
        string $position,
        int $offset = 10
    ): array {
        $positions = [
            'top-left' => ['x' => $offset, 'y' => $offset],
            'top-center' => ['x' => ($imageWidth - $watermarkWidth) / 2, 'y' => $offset],
            'top-right' => ['x' => $imageWidth - $watermarkWidth - $offset, 'y' => $offset],
            'middle-left' => ['x' => $offset, 'y' => ($imageHeight - $watermarkHeight) / 2],
            'center' => ['x' => ($imageWidth - $watermarkWidth) / 2, 'y' => ($imageHeight - $watermarkHeight) / 2],
            'middle-right' => ['x' => $imageWidth - $watermarkWidth - $offset, 'y' => ($imageHeight - $watermarkHeight) / 2],
            'bottom-left' => ['x' => $offset, 'y' => $imageHeight - $watermarkHeight - $offset],
            'bottom-center' => ['x' => ($imageWidth - $watermarkWidth) / 2, 'y' => $imageHeight - $watermarkHeight - $offset],
            'bottom-right' => ['x' => $imageWidth - $watermarkWidth - $offset, 'y' => $imageHeight - $watermarkHeight - $offset],
        ];

        return $positions[$position] ?? $positions['center'];
    }

    /**
     * Get watermark settings from database
     *
     * @return array|null
     */
    protected function getWatermarkSettings(): ?array
    {
        try {
            // Check if watermark settings table exists
            if (!\Illuminate\Support\Facades\Schema::hasTable('watermark_settings')) {
                return null;
            }

            $settings = \App\Models\WatermarkSetting::first();
            if (!$settings) {
                return null;
            }

            return [
                'enabled' => (bool) $settings->enabled,
                'watermark_image' => $settings->watermark_image,
                'size' => (int) $settings->watermark_size,
                'opacity' => (int) $settings->opacity,
                'offset' => (int) $settings->offset,
            ];
        } catch (Exception $e) {
            \Log::warning('Could not load watermark settings: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get image dimensions
     *
     * @param string $imagePath
     * @return array
     */
    public function getImageDimensions(string $imagePath): array
    {
        try {
            $fullPath = strpos($imagePath, 'storage/') === 0 
                ? storage_path('app/public/' . str_replace('storage/', '', $imagePath))
                : public_path($imagePath);

            if (!file_exists($fullPath)) {
                return ['width' => null, 'height' => null];
            }

            $image = $this->manager->read($fullPath);
            return [
                'width' => $image->width(),
                'height' => $image->height(),
            ];
        } catch (Exception $e) {
            \Log::error('Error getting image dimensions: ' . $e->getMessage());
            return ['width' => null, 'height' => null];
        }
    }
}

