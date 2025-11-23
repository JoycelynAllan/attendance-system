<?php
// debug_test.php - Quick debugging tool
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>System Debug Test</h2>";
echo "<hr>";

// Test 1: Check PHP version
echo "<h3>1. PHP Version</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
if (version_compare(phpversion(), '7.0.0', '>=')) {
    echo "<p style='color: green;'>✓ PHP version is compatible</p>";
} else {
    echo "<p style='color: red;'>✗ PHP version too old (need 7.0+)</p>";
}

// Test 2: Check required extensions
echo "<h3>2. Required PHP Extensions</h3>";
$extensions = ['pdo', 'pdo_mysql', 'json', 'mbstring'];
foreach ($extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p style='color: green;'>✓ $ext is loaded</p>";
    } else {
        echo "<p style='color: red;'>✗ $ext is NOT loaded</p>";
    }
}

// Test 3: Check file permissions
echo "<h3>3. File System</h3>";
$files = ['db_connect.php', 'signup.php', 'login.php', '.env'];
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file NOT found</p>";
    }
}

// Test 4: Database connection
echo "<h3>4. Database Connection</h3>";
try {
    require_once 'db_connect.php';
    echo "<p style='color: green;'>✓ Database connected successfully</p>";
    
    // Check tables
    $tables = ['users', 'students', 'faculty', 'courses', 'Enrollment'];
    echo "<h4>Database Tables:</h4>";
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✓ Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>✗ Table '$table' NOT found</p>";
        }
    }
    
    // Count users
    $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p>Total users in database: <strong>" . $result['count'] . "</strong></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Test 5: Check POST data handling
echo "<h3>5. POST Request Test</h3>";
echo "<form method='POST'>";
echo "<input type='text' name='test_field' placeholder='Enter test data' required>";
echo "<button type='submit'>Test POST</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_field'])) {
    echo "<p style='color: green;'>✓ POST data received: " . htmlspecialchars($_POST['test_field']) . "</p>";
}

// Test 6: Check JSON handling
echo "<h3>6. JSON Test</h3>";
$testData = ['name' => 'Test User', 'email' => 'test@example.com'];
$json = json_encode($testData);
$decoded = json_decode($json, true);
if ($decoded['name'] === 'Test User') {
    echo "<p style='color: green;'>✓ JSON encoding/decoding works</p>";
} else {
    echo "<p style='color: red;'>✗ JSON handling failed</p>";
}

// Test 7: Test password hashing
echo "<h3>7. Password Hashing Test</h3>";
$testPassword = 'password123';
$hash = password_hash($testPassword, PASSWORD_DEFAULT);
if (password_verify($testPassword, $hash)) {
    echo "<p style='color: green;'>✓ Password hashing works</p>";
} else {
    echo "<p style='color: red;'>✗ Password hashing failed</p>";
}

// Test 8: Show error log location
echo "<h3>8. Error Logging</h3>";
echo "<p>Error log location: " . ini_get('error_log') . "</p>";
echo "<p>Log errors: " . (ini_get('log_errors') ? 'Enabled' : 'Disabled') . "</p>";

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If all tests pass, try registering at <a href='signup.html'>signup.html</a></li>";
echo "<li>Open browser console (F12) to see JavaScript logs</li>";
echo "<li>Check XAMPP error logs if issues persist</li>";
echo "</ol>";
?>