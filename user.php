<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
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

<?php
$user_id = $_SESSION['user_id'];

$tickets = mysqli_query(
    $conn,
    "SELECT * FROM tickets
     WHERE user_id = $user_id
     ORDER BY created_at DESC"
);
?>

<h2>User Dashboard</h2>
<p>
  Welcome <?= htmlspecialchars($_SESSION['name']) ?>
  <span class="role-badge role-user">USER</span>
</p>
<a class="action-link" href="create_ticket.php">➕ Create New Ticket</a>
<h3>My Tickets</h3>

<div class="table-wrapper">

<table border="1" cellpadding="10">
<tr>
    <th>Ticket No</th>
    <th>Location</th>
    <th>Category</th>
    <th>Description</th>
    <th>Status</th>
    <th>Update History</th>
    <th>Created At</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($tickets)) { ?>
<tr>
    <td>
        TCK-<?= date('Y'); ?>-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?>
    </td>
    <td><?= $row['location'] ?></td>
    <td><?= $row['category'] ?></td>
    <td><?= $row['description'] ?></td>
    <td>
        <span class="status <?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
            <?= $row['status']; ?>
        </span>
    </td>
    <td class="update-history">

        <?php
        $updates = mysqli_query(
            $conn,
            "SELECT update_text, updated_at
             FROM ticket_updates
             WHERE ticket_id = {$row['id']}
             ORDER BY updated_at DESC"
        );

        if (mysqli_num_rows($updates) > 0) {
            while ($u = mysqli_fetch_assoc($updates)) {
                echo "<p><small>{$u['updated_at']}</small><br>{$u['update_text']}</p>";
            }
        } else {
            echo "<em>No updates yet</em>";
        }
        ?>
    </td>

    <td>
        <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
    </td>

</tr>
<?php } ?>
</table>

</div>

<br><br>
<a href="logout.php">Logout</a>

</div>

</body>
</html>
