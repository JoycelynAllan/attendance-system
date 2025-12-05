<?php
// db_connect.php - Database connection file

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load environment variables from .env file
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        
        // Skip comments and empty lines
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        
        // Split by first = only
        if (strpos($line, '=') === false) {
            continue;
        }
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value); // This will be empty string if DB_PASS= with nothing after
        
        // Set environment variable (empty string is valid for password)
        if (!array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
        }
    }
    return true;
}

// Load .env file
loadEnv(__DIR__ . '/.env');

// Detect if running on server
// Check by hostname/IP, HTTP_HOST, or APP_ENV
$isServer = false;

// Check HTTP_HOST (most reliable for web requests)
if (isset($_SERVER['HTTP_HOST'])) {
    $httpHost = $_SERVER['HTTP_HOST'];
    $isServer = (strpos($httpHost, '169.239.251.102') !== false || 
                 strpos($httpHost, 'joycelyn.allan') !== false ||
                 (strpos($httpHost, 'localhost') === false && strpos($httpHost, '127.0.0.1') === false));
}

// Check SERVER_NAME as fallback
if (!$isServer && isset($_SERVER['SERVER_NAME'])) {
    $serverName = $_SERVER['SERVER_NAME'];
    $isServer = ($serverName !== 'localhost' && $serverName !== '127.0.0.1');
}

// Check APP_ENV
$appEnv = getenv('APP_ENV');
if ($appEnv === 'production' || $appEnv === 'server') {
    $isServer = true;
}

// Database configuration with support for remote servers
if ($isServer && getenv('DB_HOST_SERVER') !== false) {
    // Use server credentials
    $host = getenv('DB_HOST_SERVER') ?: 'localhost';
    $port = getenv('DB_PORT_SERVER') ?: '3306';
    $dbname = getenv('DB_NAME_SERVER') ?: 'webtech_2025A_joycelyn_allan';
    $username = getenv('DB_USER_SERVER') ?: 'root';
    $password = getenv('DB_PASS_SERVER') ?: '';
} else {
    // Use localhost credentials
    $host = getenv('DB_HOST') ?: 'localhost';
    $port = getenv('DB_PORT') ?: '3306';
    $dbname = getenv('DB_NAME') ?: 'attendancemanagement';
    $username = getenv('DB_USER') ?: 'root';
    $password = getenv('DB_PASS');
}

// Handle empty password - if DB_PASS is not set or is empty string, use empty password
if ($password === false || $password === '') {
    $password = '';
}

try {
    // Create PDO connection with port support for remote servers
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 10
    ]);
    
    // Test query to verify connection
    $conn->query("SELECT 1");
    
} catch(PDOException $e) {
    // Log error and show user-friendly message
    error_log("Database Connection Error: " . $e->getMessage());
    error_log("Connection details - Host: $host, Port: $port, Database: $dbname, User: $username");
    
    // Check if this is an AJAX/API request (JSON expected)
    $isAjaxRequest = (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) || (
        !empty($_SERVER['CONTENT_TYPE']) && 
        strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
    ) || (
        !empty($_SERVER['HTTP_ACCEPT']) && 
        strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
    );
    
    // Always return JSON for API endpoints (signup.php, login.php, etc.)
    $isApiEndpoint = in_array(basename($_SERVER['PHP_SELF']), [
        'signup.php', 'login.php', 'create_course.php', 'get_courses.php',
        'join_course.php', 'manage_enrollment.php', 'create_session.php',
        'get_sessions.php', 'mark_attendance.php', 'check_in_code.php',
        'get_attendance_report.php', 'get_enrolled_students.php'
    ]);
    
    if ($isAjaxRequest || $isApiEndpoint) {
        header('Content-Type: application/json');
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed. Please check your configuration.',
            'error' => $e->getMessage(),
            'host' => $host,
            'port' => $port,
            'database' => $dbname,
            'user' => $username,
            'help' => 'Create a .env file with correct database credentials. Visit db_config_helper.php for assistance.'
        ]));
    }
    
    // Otherwise, show HTML error page for direct browser access
    die("
    <!DOCTYPE html>
    <html>
    <head><title>Database Connection Error</title>
    <style>body{font-family:Arial;max-width:600px;margin:50px auto;padding:20px;}
    .error{color:red;background:#ffe6e6;padding:15px;border-radius:5px;margin:10px 0;}
    .info{color:blue;background:#e6f3ff;padding:15px;border-radius:5px;margin:10px 0;}
    code{background:#f4f4f4;padding:2px 5px;border-radius:3px;}
    </style>
    </head>
    <body>
        <h1>Database Connection Error</h1>
        <div class='error'>
            <strong>Error:</strong> " . htmlspecialchars($e->getMessage()) . "
        </div>
        <div class='info'>
            <strong>Configuration:</strong><br>
            Host: <code>$host</code><br>
            Port: <code>$port</code><br>
            Database: <code>$dbname</code><br>
            User: <code>$username</code><br>
            <br>
            <strong>Solution:</strong> Create a <code>.env</code> file in your project root with correct database credentials.<br>
            <br>
            <a href='db_config_helper.php'>Click here for Database Configuration Helper</a>
        </div>
    </body>
    </html>
    ");
}
?>