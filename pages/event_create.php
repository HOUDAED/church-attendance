<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$userId = $_SESSION['user_id']; // id de l'utilisateur connecté

$error = '';
$success = '';

// Récupérer les églises pour la sélection
$churches = $pdo->query("SELECT id, name FROM churches ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? null;
    $location = trim($_POST['location'] ?? '');
    $church_id = $_POST['church_id'] ?? null;

    // Validation minimale
    if (!$title || !$event_date || !$church_id) {
        $error = "Please fill all required fields: Title, Date, Church.";
    }

    if (!$error) {
        $stmt = $pdo->prepare("INSERT INTO events (title, description, event_date, event_time, location, church_id, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $event_date, $event_time, $location, $church_id, $userId]);
        $success = "Event created successfully.";
        header("refresh:2;url=events.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Create New Event</title>
    <link rel="stylesheet" href="../css/dashboard.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
</head>
<body>
<nav class="navbar">
    <a href="events.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Events</a>
</nav>

<div class="container">
    <h1>Create New Event</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= h($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= h($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="form">
        <div class="form-group">
            <label>Title *</label>
            <input type="text" name="title" class="form-input" required value="<?= h($_POST['title'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-input"><?= h($_POST['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label>Date *</label>
            <input type="date" name="event_date" class="form-input" required value="<?= h($_POST['event_date'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Time</label>
            <input type="time" name="event_time" class="form-input" value="<?= h($_POST['event_time'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-input" value="<?= h($_POST['location'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label>Church *</label>
            <select name="church_id" class="form-input" required>
                <option value="">-- Select Church --</option>
                <?php foreach ($churches as $church): ?>
                    <option value="<?= $church['id'] ?>" <?= (($_POST['church_id'] ?? '') == $church['id']) ? 'selected' : '' ?>>
                        <?= h($church['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Create Event</button>
            <a href="events.php" class="btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
