<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$error = '';
$success = '';

// Fetch churches for dropdown
$churches = $pdo->query("SELECT id, name FROM churches ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $church_id = $_POST['church_id'] ?? '';
    $day = $_POST['day_of_week'] ?? '';
    $event = trim($_POST['event_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $start = $_POST['start_time'] ?? '';
    $end = $_POST['end_time'] ?? '';

    if (!$church_id || !$day || !$event || !$start || !$end) {
        $error = "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO schedules (church_id, day_of_week, event_name, description, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$church_id, $day, $event, $description, $start, $end]);
            $success = "Schedule created successfully.";
            header("refresh:2;url=schedules.php");
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Schedule</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <a href="schedules.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Schedules</a>
    </nav>

    <div class="container">
        <h1>Create New Schedule</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= h($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= h($success) ?></div>
        <?php endif; ?>

        <form method="POST" class="form">
            <div class="form-group">
                <label>Church</label>
                <select name="church_id" class="form-input" required>
                    <option value="">-- Select Church --</option>
                    <?php foreach ($churches as $c): ?>
                        <option value="<?= h($c['id']) ?>"><?= h($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Day of Week</label>
                <select name="day_of_week" class="form-input" required>
                    <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day): ?>
                        <option value="<?= $day ?>"><?= $day ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Event Name</label>
                <input type="text" name="event_name" class="form-input" required>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-input" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label>Start Time</label>
                <input type="time" name="start_time" class="form-input" required>
            </div>

            <div class="form-group">
                <label>End Time</label>
                <input type="time" name="end_time" class="form-input" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Create</button>
                <a href="schedules.php" class="btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
