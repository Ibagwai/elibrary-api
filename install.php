<?php
/**
 * K7 E-Library Installation Script
 * Upload this to your web root and visit: https://library.msit.com.ng/install.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>K7 E-Library Installation</h1>";
echo "<pre>";

// Detect if running from public folder
$baseDir = __DIR__;
if (basename($baseDir) === 'public') {
    $baseDir = dirname($baseDir);
}

echo "Base Directory: $baseDir\n\n";

// Step 1: Check if vendor exists
echo "1. Checking vendor folder... ";
if (is_dir($baseDir . '/vendor')) {
    echo "✅ EXISTS\n";
} else {
    echo "❌ MISSING - Upload vendor.tar.gz and extract it\n";
    exit;
}

// Step 2: Check .env file
echo "2. Checking .env file... ";
if (file_exists($baseDir . '/.env')) {
    echo "✅ EXISTS\n";
} else {
    echo "⚠️  MISSING - Creating from .env.shared-hosting...\n";
    if (file_exists($baseDir . '/.env.shared-hosting')) {
        copy($baseDir . '/.env.shared-hosting', $baseDir . '/.env');
        echo "   ✅ Created .env\n";
    } else {
        echo "   ❌ .env.shared-hosting not found\n";
        exit;
    }
}

// Step 3: Check APP_KEY
echo "3. Checking APP_KEY... ";
$envContent = file_get_contents($baseDir . '/.env');
if (strpos($envContent, 'APP_KEY=base64:') !== false && strlen(trim(explode('APP_KEY=', $envContent)[1])) > 20) {
    echo "✅ SET\n";
} else {
    echo "⚠️  MISSING - Generating...\n";
    chdir($baseDir);
    exec('php artisan key:generate 2>&1', $output, $return);
    if ($return === 0) {
        echo "   ✅ Generated APP_KEY\n";
    } else {
        echo "   ❌ Failed: " . implode("\n", $output) . "\n";
        echo "   Manual: Visit https://generate-random.org/laravel-key-generator\n";
        echo "   Then add to .env: APP_KEY=base64:YOUR_KEY_HERE\n";
    }
}

// Step 4: Check database file
echo "4. Checking database file... ";
$dbPath = $baseDir . '/database/database.sqlite';
if (file_exists($dbPath)) {
    echo "✅ EXISTS\n";
} else {
    echo "⚠️  MISSING - Creating...\n";
    touch($dbPath);
    chmod($dbPath, 0664);
    echo "   ✅ Created database.sqlite\n";
}

// Step 5: Check storage permissions
echo "5. Checking storage permissions... ";
$storagePath = $baseDir . '/storage';
if (is_writable($storagePath)) {
    echo "✅ WRITABLE\n";
} else {
    echo "⚠️  NOT WRITABLE - Fixing...\n";
    chmod($storagePath, 0755);
    chmod($storagePath . '/framework', 0755);
    chmod($storagePath . '/framework/cache', 0755);
    chmod($storagePath . '/framework/sessions', 0755);
    chmod($storagePath . '/framework/views', 0755);
    chmod($storagePath . '/logs', 0755);
    echo "   ✅ Fixed permissions\n";
}

// Step 6: Check bootstrap/cache permissions
echo "6. Checking bootstrap/cache permissions... ";
$cachePath = $baseDir . '/bootstrap/cache';
if (is_writable($cachePath)) {
    echo "✅ WRITABLE\n";
} else {
    echo "⚠️  NOT WRITABLE - Fixing...\n";
    chmod($cachePath, 0755);
    echo "   ✅ Fixed permissions\n";
}

// Step 7: Run migrations
echo "7. Running database migrations... ";
chdir($baseDir);
exec('php artisan migrate --seed --force 2>&1', $output, $return);
if ($return === 0) {
    echo "✅ SUCCESS\n";
    echo "   Default users created:\n";
    echo "   - admin@k7library.com / password\n";
    echo "   - faculty@k7library.com / password\n";
    echo "   - student@k7library.com / password\n";
} else {
    echo "❌ FAILED\n";
    echo "   Error: " . implode("\n   ", $output) . "\n";
}

// Step 8: Cache config
echo "8. Caching configuration... ";
exec('php artisan config:cache 2>&1', $output, $return);
if ($return === 0) {
    echo "✅ SUCCESS\n";
} else {
    echo "⚠️  Warning: " . implode("\n", $output) . "\n";
}

// Step 9: Test API
echo "\n9. Testing API health endpoint... ";
$healthUrl = 'https://library.msit.com.ng/api/health';
echo "\n   Visit: $healthUrl\n";

echo "\n";
echo "=" . str_repeat("=", 60) . "\n";
echo "Installation Complete!\n";
echo "=" . str_repeat("=", 60) . "\n";
echo "\nAPI URL: https://library.msit.com.ng/api\n";
echo "Health Check: https://library.msit.com.ng/api/health\n";
echo "Documentation: https://library.msit.com.ng/api/documentation\n";
echo "\n⚠️  IMPORTANT: Delete this install.php file after installation!\n";
echo "\n</pre>";
?>
