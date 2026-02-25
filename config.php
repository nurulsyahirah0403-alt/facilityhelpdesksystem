<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = getenv("MYSQLHOST") ?: "127.0.0.1";
$user = getenv("MYSQLUSER") ?: "root";
$pass = getenv("MYSQLPASSWORD") ?: "";
$db   = getenv("MYSQLDATABASE") ?: "helpdesk_db";
$port = getenv("MYSQLPORT") ?: 3306;

try {
    $conn = mysqli_connect($host, $user, $pass, $db, $port);
    if (!$conn) {
        throw new Exception("mysqli_connect returned false: " . mysqli_connect_error());
    }
} catch (Exception $e) {
    echo "<h1>Database Connection Error</h1>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> " . htmlspecialchars($host) . "</li>";
    echo "<li><strong>User:</strong> " . htmlspecialchars($user) . "</li>";
    echo "<li><strong>Database:</strong> " . htmlspecialchars($db) . "</li>";
    echo "<li><strong>Port:</strong> " . htmlspecialchars($port) . "</li>";
    echo "</ul>";
    die();
}
?>





