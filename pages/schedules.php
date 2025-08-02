<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

// Delete schedule
if (isset($_POST['delete_schedule'])) {
    $schedule_id = $_POST['delete_schedule'];
    $stmt = $pdo->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->execute([$schedule_id]);
    redirect('schedules.php');
}

// Fetch all schedules with church name
$query = "SELECT schedules.*, churches.name AS church_name 
          FROM schedules 
          JOIN churches ON schedules.church_id = churches.id 
          ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), start_time";
$schedules = $pdo->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Schedules - Church Management</title>
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
            <h1>Weekly Schedules</h1>
            <a href="schedule_create.php" class="add-role-btn">
                <i class="fas fa-plus"></i> Add New Schedule
            </a>
        </div>

        <table class="roles-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Church</th>
                    <th>Day</th>
                    <th>Event</th>
                    <th>Time</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $s): ?>
                    <tr>
                        <td><?= h($s['id']) ?></td>
                        <td><?= h($s['church_name']) ?></td>
                        <td><?= h($s['day_of_week']) ?></td>
                        <td><?= h($s['event_name']) ?></td>
                        <td><?= h($s['start_time']) ?> - <?= h($s['end_time']) ?></td>
                        <td><?= h($s['description']) ?></td>
                        <td>
                            <div class="role-actions">
                                <button onclick="location.href='schedule_edit.php?id=<?= h($s['id']) ?>'" class="action-icon-btn edit-btn">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" class="delete-form" onsubmit="return confirm('Delete this schedule?');" style="display:inline;">
                                    <button type="submit" name="delete_schedule" value="<?= h($s['id']) ?>" class="action-icon-btn delete-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
