<?php
$conn = mysqli_connect("localhost", "root", "");
if (!$conn) {
    die("FAILED: " . mysqli_connect_error());
}
echo "CONNECTED TO MYSQL";
?>
