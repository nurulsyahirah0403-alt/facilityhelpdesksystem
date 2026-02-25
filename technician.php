<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'technician') {
    header("Location: login.php");
    exit();
}

// Update ticket status
if (isset($_POST['update_status'])) {
    $ticket_id = $_POST['ticket_id'];
    $status = $_POST['status'];
    $update_text = $_POST['update_text'];

    // Update status ticket
    mysqli_query($conn, 
        "UPDATE tickets SET status='$status' WHERE id=$ticket_id"
    );

    // Simpan update history
    mysqli_query($conn, 
        "INSERT INTO ticket_updates (ticket_id, update_text) 
         VALUES ($ticket_id, '$update_text')"
    );
}


// Get assigned tickets only
$technician_id = $_SESSION['user_id'];

$tickets = mysqli_query($conn,
    "SELECT * FROM tickets 
     WHERE assigned_to = '$technician_id'
     ORDER BY created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Technician Dashboard</title>
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

<h2>Technician Dashboard</h2>
<p>
  Welcome <?= htmlspecialchars($_SESSION['name']) ?>
  <span class="role-badge role-technician">TECHNICIAN</span>
</p>

<div class="table-wrapper">

<table border="1" cellpadding="10">
    <tr>
        <th>Ticket No</th>
        <th>Location</th>
        <th>Category</th>
        <th>Description</th>
        <th>Status</th>
        <th>Action</th>
        <th>Image</th>
        <th>Created At</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($tickets)) { ?>
    <tr>
        <td>
            TCK-<?= date('Y'); ?>-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?>
        </td>
        <td><?= $row['location']; ?></td>
        <td><?= $row['category']; ?></td>
        <td><?= $row['description']; ?></td>
        <td>
            <span class="status <?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                <?= $row['status']; ?>
            </span>
        </td>

        <td class="action-cell">
            <form method="POST">
                <input type="hidden" name="ticket_id" value="<?= $row['id']; ?>">

<?php 
$currentStatus = $row['status']; 
?>

<select name="status" <?= ($currentStatus == 'Completed') ? 'disabled' : '' ?>>

    <option value="Open"
        <?= ($currentStatus != 'Open') ? 'disabled' : '' ?>
        <?= ($currentStatus == 'Open') ? 'selected' : '' ?>>
        Open
    </option>

    <option value="In Progress"
        <?= ($currentStatus == 'Completed') ? 'disabled' : '' ?>
        <?= ($currentStatus == 'In Progress') ? 'selected' : '' ?>>
        In Progress
    </option>

    <option value="Completed"
        <?= ($currentStatus == 'Completed') ? 'selected' : '' ?>>
        Completed
    </option>

</select>

<?php if ($currentStatus == 'Completed'): ?>
    <div style="color:green; font-size:12px; margin-top:5px;">
        ✔ Ticket Closed
    </div>
<?php endif; ?>

                <textarea name="update_text" placeholder="Enter update note" required></textarea>

                <button type="submit" name="update_status">Update</button>
            </form>
        </td>

        <td>
            <?php if (!empty($row['image'])) { ?>
            <a href="uploads/tickets/<?= $row['image']; ?>" target="_blank">
                <img src="uploads/tickets/<?= $row['image']; ?>" width="60" style="border-radius:6px;">
            </a>
            <?php } else { ?>
            No Image
            <?php } ?>
        </td>

        <td>
            <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
        </td>

    </tr>
    <?php } ?>

</table>

</div>

<br>
<a href="logout.php">Logout</a>

</div>

</body>
</html>

