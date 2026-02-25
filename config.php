<?php
$host = getenv("MYSQLHOST") ?: "localhost";
$user = getenv("MYSQLUSER") ?: "root";
$pass = getenv("MYSQLPASSWORD") ?: "";
$db   = getenv("MYSQLDATABASE") ?: "helpdesk_db";
$port = getenv("MYSQLPORT") ?: 3306;

// Explicitly avoid socket connection on Railway by passing host as string and specifying port
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
        die("Database connection failed");
}
?>
