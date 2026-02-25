<?php
session_start();
include "config.php";

if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['ticket_id']) && isset($_POST['technician_id'])) {
    $ticket_id = $_POST['ticket_id'];
    $technician_id = $_POST['technician_id'];

    $query = "UPDATE tickets 
              SET assigned_to = '$technician_id' 
              WHERE id = '$ticket_id'";

    mysqli_query($conn, $query);
}

header("Location: admin.php");
exit();