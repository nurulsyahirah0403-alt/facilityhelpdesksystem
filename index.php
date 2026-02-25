<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Facility Helpdesk System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<h1>Welcome to Facility Helpdesk System</h1>
<p>The system is ready to use.</p>

<br><br>
<a href="logout.php">Logout</a>

</body>
</html>
