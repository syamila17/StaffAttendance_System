#!/bin/bash
# Complete Page Expired Fix Script

echo "=== Staff 'Page Expired' Error Fix ==="
echo "Fixing session and CSRF issues..."
echo ""

# Navigate to project
cd "$(dirname "$0")/staff_attendance"

echo "1. Clearing all caches..."
php artisan optimize:clear
echo "✓ Caches cleared"
echo ""

echo "2. Creating session directory..."
mkdir -p storage/framework/sessions
chmod -R 755 storage/framework/sessions
echo "✓ Session directory ready"
echo ""

echo "3. Clearing compiled views..."
php artisan view:clear
echo "✓ Views cleared"
echo ""

echo "4. Verifying middleware configuration..."
php artisan tinker <<'EOF'
$middleware = config('session');
echo "Session Configuration:\n";
echo "- Driver: " . $middleware['driver'] . "\n";
echo "- Lifetime: " . $middleware['lifetime'] . " minutes\n";
echo "- Secure: " . ($middleware['secure'] ? 'Yes' : 'No') . "\n";
echo "- HTTP Only: " . ($middleware['http_only'] ? 'Yes' : 'No') . "\n";
echo "- Same Site: " . $middleware['same_site'] . "\n";
exit()
EOF
echo ""

echo "5. Testing session creation..."
php artisan tinker <<'EOF'
session()->put('test_session', 'Working');
$result = session()->get('test_session');
if ($result === 'Working') {
    echo "✓ Session creation: WORKING\n";
} else {
    echo "✗ Session creation: FAILED\n";
}
exit()
EOF
echo ""

echo "=== Fix Complete ==="
echo ""
echo "Next steps:"
echo "1. Restart Laravel server: php artisan serve"
echo "2. Clear browser cookies for localhost:8000"
echo "3. Test staff management page"
echo ""
