<?php
// Test file upload - upload to: library.msit.com.ng/public/test-upload.php

require __DIR__.'/../vendor/autoload.php';

echo "<h1>Storage Diagnostic</h1>";
echo "<pre>";

$baseDir = dirname(__DIR__);

// Check storage directories
echo "1. Storage directories:\n";
$dirs = [
    'storage/app/public',
    'storage/app/public/content',
    'storage/app/public/thumbnails',
    'public/storage',
];

foreach ($dirs as $dir) {
    $path = $baseDir . '/' . $dir;
    $exists = is_dir($path);
    $writable = $exists && is_writable($path);
    echo "   $dir: " . ($exists ? '✅ EXISTS' : '❌ MISSING');
    if ($exists) {
        echo " | " . ($writable ? '✅ WRITABLE' : '❌ NOT WRITABLE');
        echo " | Perms: " . substr(sprintf('%o', fileperms($path)), -4);
    }
    echo "\n";
}

// Check storage link
echo "\n2. Storage link:\n";
$linkPath = $baseDir . '/public/storage';
if (is_link($linkPath)) {
    echo "   ✅ Symlink exists\n";
    echo "   Points to: " . readlink($linkPath) . "\n";
} else if (is_dir($linkPath)) {
    echo "   ⚠️  Directory exists (not a symlink)\n";
} else {
    echo "   ❌ Storage link missing\n";
    echo "   Creating link...\n";
    
    $target = $baseDir . '/storage/app/public';
    if (symlink($target, $linkPath)) {
        echo "   ✅ Link created successfully!\n";
    } else {
        echo "   ❌ Failed to create link\n";
        echo "   Manual: ln -s $target $linkPath\n";
    }
}

// Test file write
echo "\n3. Test file write:\n";
$testFile = $baseDir . '/storage/app/public/test.txt';
$written = file_put_contents($testFile, 'test');
if ($written) {
    echo "   ✅ Can write to storage\n";
    unlink($testFile);
} else {
    echo "   ❌ Cannot write to storage\n";
}

// Check recent uploads
echo "\n4. Recent uploads:\n";
$contentDir = $baseDir . '/storage/app/public/content';
if (is_dir($contentDir)) {
    $files = array_diff(scandir($contentDir), ['.', '..']);
    if (count($files) > 0) {
        echo "   Found " . count($files) . " files:\n";
        foreach (array_slice($files, 0, 5) as $file) {
            echo "   - $file\n";
        }
    } else {
        echo "   No files uploaded yet\n";
    }
} else {
    echo "   Content directory doesn't exist\n";
}

echo "\n5. Config:\n";
echo "   APP_URL: " . env('APP_URL') . "\n";
echo "   FILESYSTEM_DISK: " . env('FILESYSTEM_DISK', 'local') . "\n";

echo "</pre>";
?>
