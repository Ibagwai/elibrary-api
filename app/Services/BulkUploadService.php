<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BulkUploadService
{
    public function processZipUpload(UploadedFile $file): array
    {
        $results = [
            'success' => [],
            'failed' => [],
        ];

        $zip = new ZipArchive;
        $tempPath = $file->getRealPath();

        if ($zip->open($tempPath) === TRUE) {
            $extractPath = storage_path('app/temp/' . uniqid());
            $zip->extractTo($extractPath);
            $zip->close();

            // Process each file
            $files = glob($extractPath . '/*');
            
            foreach ($files as $filePath) {
                try {
                    $fileName = basename($filePath);
                    
                    // Skip non-PDF files for now
                    if (!str_ends_with(strtolower($fileName), '.pdf')) {
                        continue;
                    }

                    // Store file
                    $storedPath = Storage::putFileAs(
                        'content',
                        new \Illuminate\Http\File($filePath),
                        $fileName
                    );

                    $results['success'][] = [
                        'file' => $fileName,
                        'path' => $storedPath,
                    ];
                } catch (\Exception $e) {
                    $results['failed'][] = [
                        'file' => basename($filePath),
                        'error' => $e->getMessage(),
                    ];
                }
            }

            // Cleanup
            $this->deleteDirectory($extractPath);
        }

        return $results;
    }

    private function deleteDirectory(string $dir): void
    {
        if (!file_exists($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        
        rmdir($dir);
    }
}
