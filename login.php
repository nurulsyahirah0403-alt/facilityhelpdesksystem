<?php
session_start();
include "config.php";

if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        if ($password == $row['password']) {

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role'] = $row['role'];
            $_SESSION['name'] = $row['name'];

            if ($row['role'] == 'admin') {
                header("Location: admin.php");
            } elseif ($row['role'] == 'technician') {
                header("Location: technician.php");
            } else {
                header("Location: user.php");
            }
            exit();

        } else {
            $error = "Wrong password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        <div class="login-logo">
            <img src="assets/images/kktm-logo.jpg" alt="KKTM Logo">
            <h1 class="system-name">Helpdesk System</h1>
        </div>

        <p class="login-subtitle">A Support Portal for Classroom & Laboratoty Facilities</p>

        <h2>Login</h2>

        <p class="login-hint">Admin / Technician / User access supported</p>

        <form method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
            <div class="password-wrapper">
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <span class="toggle-password" onclick="togglePassword()" aria-label="Show password">👁</span>
            </div>

            <button type="submit" name="login" class="btn-login">
                Login
            </button>
        </form>

        <?php
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        ?>

    </div>
</div>

<?php
if (isset($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<script>
function togglePassword() {
    const pwd = document.getElementById("password");
    pwd.type = pwd.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
