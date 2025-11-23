<?php
// check_files.php - Check which files exist and where

echo "<h2>File Location Checker</h2>";
echo "<p>Checking file locations in your attendance system folder...</p>";
echo "<hr>";

// Files to check
$filesToCheck = [
    'css/style.css',
    'css/dashboard.css',
    'js/signup.js',
    'js/login.js',
    'js/logout.js',
    'requests/css/style.css',
    'requests/css/dashboard.css',
    'requests/js/signup.js',
    'requests/js/login.js',
    'requests/js/logout.js'
];

echo "<h3>File Status:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th style='padding: 10px;'>File Path</th><th style='padding: 10px;'>Status</th></tr>";

foreach ($filesToCheck as $file) {
    $exists = file_exists($file);
    $status = $exists 
        ? "<span style='color: green; font-weight: bold;'>✓ EXISTS</span>" 
        : "<span style='color: red;'>✗ NOT FOUND</span>";
    
    echo "<tr>";
    echo "<td style='padding: 10px;'>" . htmlspecialchars($file) . "</td>";
    echo "<td style='padding: 10px;'>" . $status . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h3>Recommendations:</h3>";

$cssExists = file_exists('css/style.css');
$requestsCssExists = file_exists('requests/css/style.css');

if ($cssExists && !$requestsCssExists) {
    echo "<p style='color: green;'>✓ Files are in <strong>css/</strong> and <strong>js/</strong> folders - your HTML should use:<br>";
    echo "<code>href='css/style.css'</code> and <code>src='js/login.js'</code></p>";
} elseif ($requestsCssExists && !$cssExists) {
    echo "<p style='color: orange;'>⚠ Files are in <strong>requests/css/</strong> and <strong>requests/js/</strong> folders - your HTML should use:<br>";
    echo "<code>href='requests/css/style.css'</code> and <code>src='requests/js/login.js'</code></p>";
} elseif ($cssExists && $requestsCssExists) {
    echo "<p style='color: blue;'>ℹ Files exist in BOTH locations. Recommend using <strong>css/</strong> and <strong>js/</strong> (standard structure).</p>";
} else {
    echo "<p style='color: red;'>✗ CSS and JS files not found in either location! Please check your folder structure.</p>";
}

echo "<hr>";
echo "<h3>Current Directory:</h3>";
echo "<p>" . getcwd() . "</p>";

echo "<h3>All Files in Current Directory:</h3>";
echo "<ul>";
$files = scandir('.');
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        $type = is_dir($file) ? '[DIR]' : '[FILE]';
        echo "<li>$type $file</li>";
    }
}
echo "</ul>";
?>