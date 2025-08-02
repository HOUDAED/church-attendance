<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$error = '';
$success = '';

// Récupérer les églises et rôles
$churches = $pdo->query("SELECT id, name FROM churches ORDER BY name")->fetchAll();
$roles = $pdo->query("SELECT id, name FROM roles ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $birth_date = $_POST['birth_date'] ?? null;
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address_line1 = trim($_POST['address_line1'] ?? '');
    $address_line2 = trim($_POST['address_line2'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $role_id = $_POST['role_id'] ?? null;
    $church_id = $_POST['church_id'] ?? null;

    // PHOTO upload
    $photo = null;
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = '../uploads/members/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $fileInfo = pathinfo($_FILES['photo']['name']);
        $ext = strtolower($fileInfo['extension']);

        if (in_array($ext, $allowedExt)) {
            $newFileName = uniqid('member_') . '.' . $ext;
            $uploadFile = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadFile)) {
                $photo = 'uploads/members/' . $newFileName; // chemin relatif, sans ../
            } else {
                $error = "Failed to upload photo.";
            }
        } else {
            $error = "Invalid photo format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    if (empty($first_name) || empty($last_name) || empty($gender) || empty($church_id)) {
        $error = "First name, last name, gender, and church are required.";
    }

    if (!$error) {
        $stmt = $pdo->prepare("INSERT INTO members 
            (first_name, last_name, gender, birth_date, phone, email, photo, address_line1, address_line2, city, status, role_id, church_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $first_name, $last_name, $gender, $birth_date, $phone, $email, $photo,
            $address_line1, $address_line2, $city, $status, $role_id, $church_id
        ]);
        $success = "Member added successfully.";
        header("refresh:2;url=members.php");
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Member</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/churches.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <a href="members.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Members</a>
</nav>

<div class="container">
    <h1>Add New Member</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= h($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= h($success) ?></div>
    <?php endif; ?>

    <form method="POST" class="form" enctype="multipart/form-data">
        <div class="form-group">
            <label>First Name *</label>
            <input type="text" name="first_name" class="form-input" required>
        </div>

        <div class="form-group">
            <label>Last Name *</label>
            <input type="text" name="last_name" class="form-input" required>
        </div>

        <div class="form-group">
            <label>Gender *</label>
            <select name="gender" class="form-input" required>
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Birth Date</label>
            <input type="date" name="birth_date" class="form-input">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" class="form-input">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-input">
        </div>

        <div class="form-group">
            <label>Photo</label>
            <input type="file" name="photo" class="form-input">
        </div>

        <div class="form-group">
            <label>Address Line 1</label>
            <input type="text" name="address_line1" class="form-input">
        </div>

        <div class="form-group">
            <label>Address Line 2</label>
            <input type="text" name="address_line2" class="form-input">
        </div>

        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" class="form-input">
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-input">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="transferred">Transferred</option>
                <option value="deceased">Deceased</option>
            </select>
        </div>

        <div class="form-group">
            <label>Role</label>
            <select name="role_id" class="form-input">
                <option value="">-- None --</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>"><?= h($role['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Church *</label>
            <select name="church_id" class="form-input" required>
                <option value="">-- Select Church --</option>
                <?php foreach ($churches as $church): ?>
                    <option value="<?= $church['id'] ?>"><?= h($church['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Create Member</button>
            <a href="members.php" class="btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
