<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_POST['add_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already exists!";
    } else {
        mysqli_query($conn, "
            INSERT INTO users (name, email, password, role)
            VALUES ('$name', '$email', '$password', '$role')
        ");
        $success = "User added successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Add New User</h2>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>

    <form method="POST">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
        </div>

        <div class="password-wrapper">
            <input 
            type="password" 
            name="password" 
            id="addPassword"
            placeholder="Enter password"
            required
            >
            <span class="toggle-password" onclick="toggleAddPassword()">👁</span>
        </div>


        <div class="form-group">
            <label>Role</label>
            <select name="role" required>
                <option value="user">User</option>
                <option value="technician">Technician</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" name="add_user">Add User</button>
    </form>

    <br>
    <a href="admin.php">⬅ Back to Admin Dashboard</a>
</div>

<script>
function toggleAddPassword() {
    const pwd = document.getElementById("addPassword");
    pwd.type = pwd.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
