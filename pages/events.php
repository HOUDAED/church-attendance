<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

// Supprimer un événement
if (isset($_POST['delete_event'])) {
    $event_id = $_POST['delete_event'];
    $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
    $stmt->execute([$event_id]);
    redirect('events.php');
}

// Récupérer tous les événements avec le nom de l’église
$query = "
    SELECT e.*, c.name AS church_name, u.username AS creator_name
    FROM events e
    LEFT JOIN churches c ON e.church_id = c.id
    LEFT JOIN users u ON e.created_by = u.id
    ORDER BY e.event_date DESC, e.event_time DESC
";
$events = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Events - Church Management</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <div class="roles-container">
        <div class="roles-header">
            <h1>All Events</h1>
            <a href="event_create.php" class="add-role-btn">
                <i class="fas fa-plus"></i> Add New Event
            </a>
        </div>

        <table class="roles-table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Location</th>
                    <th>Church</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $e): ?>
                    <tr>
                        <td><?= h($e['title']) ?></td>
                        <td><?= h($e['event_date']) ?></td>
                        <td><?= h($e['event_time']) ?></td>
                        <td><?= h($e['location']) ?></td>
                        <td><?= h($e['church_name']) ?></td>
                        <td><?= h($e['creator_name']) ?></td>
                        <td>
                            <a href="event_view.php?id=<?= $e['id'] ?>" class="btn-small">View</a>
                            <a href="event_edit.php?id=<?= $e['id'] ?>" class="btn-small">Edit</a>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this event?');">
                                <button type="submit" name="delete_event" value="<?= $e['id'] ?>" class="btn-small danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
</body>
</html>
