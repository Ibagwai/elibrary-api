<?php
// Simple PHP diagnostic - upload to root and visit: library.msit.com.ng/check.php

echo "<h1>PHP Configuration Check</h1>";
echo "<pre>";

echo "PHP Version: " . phpversion() . "\n\n";

echo "Required Extensions:\n";
$required = ['mbstring', 'PDO', 'pdo_mysql', 'fileinfo', 'openssl', 'tokenizer', 'xml', 'ctype', 'json'];
foreach ($required as $ext) {
    $loaded = extension_loaded($ext);
    echo "  " . ($loaded ? "✅" : "❌") . " $ext\n";
}

echo "\n\nAll Loaded Extensions:\n";
print_r(get_loaded_extensions());

echo "\n\nmbstring functions available:\n";
echo "  mb_split: " . (function_exists('mb_split') ? "✅ YES" : "❌ NO") . "\n";
echo "  mb_strlen: " . (function_exists('mb_strlen') ? "✅ YES" : "❌ NO") . "\n";

echo "\n\nPHP Info:\n";
echo "  Display Errors: " . ini_get('display_errors') . "\n";
echo "  Error Reporting: " . error_reporting() . "\n";

echo "</pre>";
?>
