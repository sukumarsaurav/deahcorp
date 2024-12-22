<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>File Structure Check</h1>";

$required_files = [
    '/includes/Config.php',
    '/includes/Database.php',
    '/includes/config/config.php',
    '/includes/config/production.php'
];

$base_path = __DIR__;
echo "<p>Base Path: " . $base_path . "</p>";

echo "<h2>Required Files:</h2>";
echo "<ul>";
foreach ($required_files as $file) {
    $full_path = $base_path . $file;
    echo "<li>";
    echo $file . ": ";
    if (file_exists($full_path)) {
        echo "<span style='color: green;'>Found</span>";
        echo " (Permissions: " . substr(sprintf('%o', fileperms($full_path)), -4) . ")";
    } else {
        echo "<span style='color: red;'>Missing</span>";
    }
    echo "</li>";
}
echo "</ul>";

echo "<h2>Directory Contents:</h2>";
function list_directory($path, $indent = 0) {
    $files = scandir($path);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo str_repeat("&nbsp;", $indent * 4) . "- " . $file;
            if (is_dir($path . '/' . $file)) {
                echo " (directory)";
                echo "<br>";
                list_directory($path . '/' . $file, $indent + 1);
            } else {
                echo "<br>";
            }
        }
    }
}

list_directory($base_path); 