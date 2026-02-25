<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
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

<h2>Admin Dashboard</h2>
<a href="add_user.php" class="action-link">➕ Add New User</a>
<br><br>
<p>
  Welcome <?= htmlspecialchars($_SESSION['name']) ?>
  <span class="role-badge role-admin">ADMIN</span>
</p>

<?php
$open = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM tickets WHERE status='Open'")
)['total'];

$progress = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM tickets WHERE status='In Progress'")
)['total'];

$done = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM tickets WHERE status='Completed'")
)['total'];
?>

<div class="stats">
    <div class="card open">Open: <?= $open ?></div>
    <div class="card progress">In Progress: <?= $progress ?></div>
    <div class="card done">Completed: <?= $done ?></div>
</div>

<?php
$search = $_GET['search'] ?? '';

$query = "SELECT tickets.id,
          tickets.location,
          tickets.category,
          tickets.description,
          tickets.status,
          tickets.assigned_to,
          users.name AS user_name
          FROM tickets
          LEFT JOIN users ON tickets.user_id = users.id";

if ($search != '') {
    $query .= " WHERE tickets.id LIKE '%$search%'
                OR tickets.location LIKE '%$search%'
                OR users.name LIKE '%$search%'";
}

$result = mysqli_query($conn, $query);
?>

<h3>All Tickets</h3>

<form method="GET" style="margin-bottom:15px;">
    <input type="text" name="search" placeholder="Search ticket..." value="<?= $_GET['search'] ?? '' ?>">
    <button type="submit">Search</button>
</form>

<div class="table-wrapper">

<table border="1" cellpadding="10">
<tr>
    <th>Ticket No</th>
    <th>User</th>
    <th>Location</th>
    <th>Category</th>
    <th>Description</th>
    <th>Status</th>
    <th>Update History</th>
    <th>Assign To</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td>
        TCK-<?= date('Y'); ?>-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?>
    </td>
    <td><?= $row['user_name']; ?></td>
    <td><?= $row['location']; ?></td>
    <td><?= $row['category']; ?></td>
    <td><?= $row['description']; ?></td>
    <td>
        <span class="status <?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
            <?= $row['status']; ?>
        </span>
    </td>

    <td class="update-history">
        <?php
        $updateQuery = mysqli_query(
            $conn,
            "SELECT update_text, updated_at 
            FROM ticket_updates 
            WHERE ticket_id = {$row['id']}
            ORDER BY updated_at DESC"
        );

        if (mysqli_num_rows($updateQuery) > 0) {
            while ($u = mysqli_fetch_assoc($updateQuery)) {
                echo "<p><small>{$u['updated_at']}</small><br>{$u['update_text']}</p>";
            }
        } else {
            echo "<em>No updates yet</em>";
        }
        ?>
    </td>

    <td class="action-cell">
    <form method="post" action="assign_ticket.php">
        <input type="hidden" name="ticket_id" value="<?= $row['id']; ?>">

        <select name="technician_id" <?= (!empty($row['assigned_to'])) ? "disabled" : "" ?>>
            <?php
            $techQuery = mysqli_query($conn, "SELECT id, name FROM users WHERE role='technician'");
            while ($tech = mysqli_fetch_assoc($techQuery)) {

                $selected = (!empty($row['assigned_to']) && $tech['id'] == $row['assigned_to']) ? "selected" : "";

                echo "<option value='{$tech['id']}' $selected>
                        {$tech['name']}
                      </option>";
            }
            ?>
        </select>

        <button type="submit"
            <?= (!empty($row['assigned_to'])) ? "disabled" : "" ?>>
            <?= (!empty($row['assigned_to'])) ? "Assigned" : "Assign" ?>
        </button>
    </form>
</td>

</tr>
<?php } ?>
</table>

</div>

<hr>

<a href="logout.php">Logout</a>

</div>

</body>
</html>
