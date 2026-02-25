<?php
session_start();
include "config.php";

/* Pastikan hanya USER boleh create ticket */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['submit_ticket'])) {

    $user_id = $_SESSION['user_id'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $imageName = NULL;

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $targetDir = "uploads/tickets/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $imageName = time() . "_" . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $imageName);
}

$query = "INSERT INTO tickets 
(user_id, location, category, description, status, image)
VALUES 
('$user_id', '$location', '$category', '$description', 'Open', '$imageName')";
mysqli_query($conn, $query);

    $success = "Ticket berjaya dihantar!";
    
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Ticket</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
<div class="navbar">
    <div class="logo">Helpdesk System</div>
    <div class="nav-links">
        <a href="logout.php">Logout</a>
    </div>
</div>

<h2>Create New Ticket</h2>

<?php
if (isset($success)) {
    echo "<p style='color:green;'>$success</p>";
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Location</label><br>
    <input type="text" name="location" required><br><br>

    <label>Category</label><br>
    <input type="text" name="category" required><br><br>

    <label>Description</label><br>
    <textarea name="description" required></textarea><br><br>

<div class="form-group">
    <label>Upload Image (Optional)</label>
    <input type="file" name="image" accept="image/*">
</div>

    <button type="submit" name="submit_ticket">Submit Ticket</button>
</form>

<br>
<a href="user.php">Back to Dashboard</a>

</div>

</body>
</html>
