#!/usr/bin/env php
<?php
/**
 * Verify Session Configuration
 * Run: php verify_session_config.php
 */

echo "\n";
echo "════════════════════════════════════════════════════════\n";
echo "  SESSION CONFIGURATION VERIFICATION\n";
echo "════════════════════════════════════════════════════════\n\n";

// Check if .env file has correct settings
$envFile = '.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    
    echo "✓ .env file found\n\n";
    
    // Check each setting
    $settings = [
        'SESSION_DRIVER' => 'file',
        'SESSION_LIFETIME' => '1440',
        'SESSION_EXPIRE_ON_CLOSE' => 'false',
        'SESSION_SAME_SITE' => 'lax',
        'SESSION_HTTP_ONLY' => 'true',
        'SESSION_ENCRYPT' => 'false',
    ];
    
    echo "Checking .env settings:\n";
    echo str_repeat("-", 60) . "\n";
    
    foreach ($settings as $setting => $expected) {
        if (strpos($envContent, "{$setting}={$expected}") !== false) {
            echo "✓ {$setting}={$expected}\n";
        } else {
            echo "✗ {$setting} (expected: {$expected})\n";
        }
    }
    
    echo "\n";
} else {
    echo "✗ .env file not found\n\n";
}

// Check session directory
$sessionDir = 'storage/framework/sessions';
if (is_dir($sessionDir)) {
    echo "✓ Session directory exists: {$sessionDir}\n";
    $sessionFiles = count(glob("{$sessionDir}/*"));
    echo "  Session files: {$sessionFiles}\n";
} else {
    echo "✗ Session directory not found: {$sessionDir}\n";
}

echo "\n";

// Check middleware files
$middlewareFiles = [
    'app/Http/Middleware/RegenerateToken.php',
    'app/Http/Middleware/EnsureSessionIntegrity.php',
];

echo "Checking middleware files:\n";
echo str_repeat("-", 60) . "\n";

foreach ($middlewareFiles as $file) {
    if (file_exists($file)) {
        echo "✓ {$file}\n";
    } else {
        echo "✗ {$file} NOT FOUND\n";
    }
}

echo "\n";

// Check Kernel.php
$kernelFile = 'app/Http/Kernel.php';
if (file_exists($kernelFile)) {
    $kernelContent = file_get_contents($kernelFile);
    
    echo "Checking Kernel.php middleware:\n";
    echo str_repeat("-", 60) . "\n";
    
    $middlewares = [
        'RegenerateToken::class',
        'EnsureSessionIntegrity::class',
    ];
    
    foreach ($middlewares as $middleware) {
        if (strpos($kernelContent, $middleware) !== false) {
            echo "✓ {$middleware}\n";
        } else {
            echo "✗ {$middleware} NOT IN KERNEL\n";
        }
    }
} else {
    echo "✗ Kernel.php not found\n";
}

echo "\n";
echo "════════════════════════════════════════════════════════\n";
echo "  NEXT STEPS:\n";
echo "════════════════════════════════════════════════════════\n";
echo "1. Restart Laravel server: php artisan serve\n";
echo "2. Clear browser cache and cookies\n";
echo "3. Test login and staff management pages\n";
echo "4. Check for 'page expired' errors\n";
echo "\n";
