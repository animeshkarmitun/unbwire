<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait FileUploadTrait
{
    /**
     * Handle file upload
     *
     * @param Request $request
     * @param string $fieldName
     * @param string|null $oldPath
     * @return string|null
     */
    public function handleFileUpload(Request $request, string $fieldName, ?string $oldPath = null): ?string
    {
        if (!$request->hasFile($fieldName)) {
            return $oldPath;
        }

        $file = $request->file($fieldName);

        // Validate file
        if (!$file->isValid()) {
            return $oldPath;
        }

        // Delete old file if exists
        if ($oldPath) {
            // Handle both storage paths and public paths
            $oldPathToDelete = str_replace('storage/', '', $oldPath);
            if (Storage::disk('public')->exists($oldPathToDelete)) {
                Storage::disk('public')->delete($oldPathToDelete);
            } elseif (file_exists(public_path($oldPath))) {
                unlink(public_path($oldPath));
            }
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::random(40) . '.' . $extension;

        // Store file in public disk
        $storedPath = $file->storeAs('uploads', $filename, 'public');

        // Return path compatible with asset() helper (storage/uploads/filename)
        return 'storage/' . $storedPath;
    }

    /**
     * Delete a file from storage
     *
     * @param string|null $filePath
     * @return bool
     */
    public function deleteFile(?string $filePath): bool
    {
        if (empty($filePath)) {
            return false;
        }

        try {
            // Handle both storage paths and public paths
            $pathToDelete = str_replace('storage/', '', $filePath);
            
            // Try to delete from storage disk first
            if (Storage::disk('public')->exists($pathToDelete)) {
                return Storage::disk('public')->delete($pathToDelete);
            }
            
            // Fallback to public path
            if (file_exists(public_path($filePath))) {
                return unlink(public_path($filePath));
            }
            
            return false;
        } catch (\Exception $e) {
            // Log error but don't throw exception
            \Log::error('Error deleting file: ' . $filePath . ' - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Move a file to archive folder
     *
     * @param string|null $filePath
     * @return string|null New path in archive folder, or null if file doesn't exist
     */
    public function moveFileToArchive(?string $filePath): ?string
    {
        if (empty($filePath)) {
            return null;
        }

        try {
            $sourcePath = str_replace('storage/', '', $filePath);
            $archivePath = 'archive/' . $sourcePath;
            
            // Create archive directory if it doesn't exist
            $archiveDir = dirname($archivePath);
            if (!Storage::disk('public')->exists($archiveDir)) {
                Storage::disk('public')->makeDirectory($archiveDir, 0755, true);
            }
            
            // Check if file exists in storage
            if (Storage::disk('public')->exists($sourcePath)) {
                // Move file to archive folder
                Storage::disk('public')->move($sourcePath, $archivePath);
                return 'storage/' . $archivePath;
            }
            
            // Fallback: try public path
            $publicSourcePath = public_path($filePath);
            if (file_exists($publicSourcePath)) {
                $publicArchivePath = public_path('storage/archive/' . basename($filePath));
                $publicArchiveDir = dirname($publicArchivePath);
                
                // Create archive directory in public
                if (!is_dir($publicArchiveDir)) {
                    mkdir($publicArchiveDir, 0755, true);
                }
                
                // Move file
                if (rename($publicSourcePath, $publicArchivePath)) {
                    return 'storage/archive/' . basename($filePath);
                }
            }
            
            return null;
        } catch (\Exception $e) {
            // Log error but don't throw exception
            \Log::error('Error moving file to archive: ' . $filePath . ' - ' . $e->getMessage());
            return null;
        }
    }
}

