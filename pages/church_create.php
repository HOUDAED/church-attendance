<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $foundation_date = $_POST['foundation_date'] ?? null;
    $description = trim($_POST['description'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');

    // Handle image upload (optional)
    $image = null;
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = '../uploads/churches/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . time() . '_' . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $image = str_replace('../', '', $imagePath); // store relative path
        } else {
            $error = "Failed to upload image.";
        }
    }

    if (empty($name)) {
        $error = "Church name is required";
    }

    if (!$error) {
        try {
            $stmt = $pdo->prepare("INSERT INTO churches (name, location, foundation_date, image, description, contact_email, contact_phone) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $location, $foundation_date, $image, $description, $contact_email, $contact_phone]);
            $success = "Church created successfully";
            header("refresh:2;url=churches.php");
        } catch (PDOException $e) {
            $error = "Error creating church: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Church</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/churches.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <a href="churches.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Churches</a>
</nav>

<div class="container">
    <h1>Add New Church</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= h($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= h($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="form" enctype="multipart/form-data">
        <div class="form-group">
            <label>Church Name</label>
            <input type="text" name="name" class="form-input" required>
        </div>

        <div class="form-group">
            <label>Location</label>
            <input type="text" name="location" class="form-input">
        </div>

        <div class="form-group">
            <label>Foundation Date</label>
            <input type="date" name="foundation_date" class="form-input">
        </div>

        <div class="form-group">
            <label>Church Image</label>
            <input type="file" name="image" class="form-input">
        </div>

        <div class="form-group">
            <label>Description</label>
            <textarea name="description" class="form-input" rows="3"></textarea>
        </div>

        <div class="form-group">
            <label>Contact Email</label>
            <input type="email" name="contact_email" class="form-input">
        </div>

        <div class="form-group">
            <label>Contact Phone</label>
            <input type="tel" name="contact_phone" class="form-input">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Create Church</button>
            <a href="churches.php" class="btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
