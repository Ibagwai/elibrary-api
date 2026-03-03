<?php
$baseDir = dirname(__DIR__);

echo "<h1>Fixing Storage...</h1><pre>";

// Create directories
$dirs = [
    'storage/app/public/content',
    'storage/app/public/thumbnails',
];

foreach ($dirs as $dir) {
    $path = $baseDir . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
        echo "✅ Created: $dir\n";
    } else {
        echo "✅ Already exists: $dir\n";
    }
    chmod($path, 0777);
}

echo "\n✅ All directories created!\n";
echo "\nNow update .env:\n";
echo "APP_URL=https://library.msit.com.ng\n";
echo "FILESYSTEM_DISK=public\n";
echo "\nThen delete: bootstrap/cache/config.php\n";
echo "</pre>";
?>
