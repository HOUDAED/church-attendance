<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if (!isset($_GET['id'])) {
    redirect('members.php');
}

$id = (int) $_GET['id'];
$error = '';
$success = '';

// Récupérer les rôles et églises
$roles = $pdo->query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
$churches = $pdo->query("SELECT id, name FROM churches ORDER BY name")->fetchAll();

// Récupérer les données actuelles
$stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();

if (!$member) {
    redirect('members.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $gender = $_POST['gender'];
    $birth_date = $_POST['birth_date'] ?? null;
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $role_id = $_POST['role_id'] ?: null;
    $church_id = $_POST['church_id'] ?: null;
    $status = $_POST['status'];
    $address_line1 = trim($_POST['address_line1']);
    $address_line2 = trim($_POST['address_line2']);
    $city = trim($_POST['city']);

    $photo = $member['photo']; // garder l'ancienne par défaut

    // Gestion upload photo
    if (!empty($_FILES['photo']['name'])) {
        $uploadDir = realpath(__DIR__ . '/../uploads/members') . '/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $fileInfo = pathinfo($_FILES['photo']['name']);
        $ext = strtolower($fileInfo['extension']);

        if (in_array($ext, $allowedExt)) {
            $newFileName = uniqid('member_') . '.' . $ext;
            $photoPath = $uploadDir . $newFileName;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath)) {
                $photo = 'uploads/members/' . $newFileName;
            } else {
                $error = "Photo upload failed.";
            }
        } else {
            $error = "Invalid photo format. Allowed: jpg, jpeg, png, gif.";
        }
    }

    if (empty($first_name) || empty($last_name)) {
        $error = "First and last names are required.";
    }

    if (!$error) {
        try {
            $stmt = $pdo->prepare("UPDATE members SET 
                first_name = ?, last_name = ?, gender = ?, birth_date = ?, phone = ?, email = ?, 
                role_id = ?, church_id = ?, status = ?, address_line1 = ?, address_line2 = ?, city = ?, photo = ?
                WHERE id = ?");
            $stmt->execute([
                $first_name, $last_name, $gender, $birth_date, $phone, $email,
                $role_id, $church_id, $status, $address_line1, $address_line2, $city, $photo,
                $id
            ]);
            $success = "Member updated successfully.";
            header("refresh:2;url=members.php");
        } catch (PDOException $e) {
            $error = "Update failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Member</title>
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/churches.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<nav class="navbar">
    <a href="members.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Members</a>
</nav>

<div class="container">
    <h1>Edit Member</h1>

    <?php if ($error): ?>
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= h($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= h($success) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="form">
        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" value="<?= h($member['first_name']) ?>" class="form-input" required>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" value="<?= h($member['last_name']) ?>" class="form-input" required>
        </div>

        <div class="form-group">
            <label>Gender</label>
            <select name="gender" class="form-input" required>
                <option value="Male" <?= $member['gender'] === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= $member['gender'] === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label>Birth Date</label>
            <input type="date" name="birth_date" value="<?= h($member['birth_date']) ?>" class="form-input">
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= h($member['phone']) ?>" class="form-input">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= h($member['email']) ?>" class="form-input">
        </div>

        <div class="form-group">
            <label>Role</label>
            <select name="role_id" class="form-input">
                <option value="">-- Select Role --</option>
                <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id'] ?>" <?= $member['role_id'] == $role['id'] ? 'selected' : '' ?>>
                        <?= h($role['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Church</label>
            <select name="church_id" class="form-input" required>
                <option value="">-- Select Church --</option>
                <?php foreach ($churches as $church): ?>
                    <option value="<?= $church['id'] ?>" <?= $member['church_id'] == $church['id'] ? 'selected' : '' ?>>
                        <?= h($church['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" class="form-input" required>
                <option value="active" <?= $member['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= $member['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                <option value="transferred" <?= $member['status'] === 'transferred' ? 'selected' : '' ?>>Transferred</option>
                <option value="deceased" <?= $member['status'] === 'deceased' ? 'selected' : '' ?>>Deceased</option>
            </select>
        </div>

        <div class="form-group">
            <label>Address Line 1</label>
            <input type="text" name="address_line1" value="<?= h($member['address_line1']) ?>" class="form-input">
        </div>

        <div class="form-group">
            <label>Address Line 2</label>
            <input type="text" name="address_line2" value="<?= h($member['address_line2']) ?>" class="form-input">
        </div>

        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" value="<?= h($member['city']) ?>" class="form-input">
        </div>

        <div class="form-group">
            <label>Photo</label><br>
            <?php if ($member['photo']): ?>
                <img src="../<?= h($member['photo']) ?>" alt="Current Photo" width="80" style="border-radius:4px; margin-bottom:10px;"><br>
            <?php endif; ?>
            <input type="file" name="photo" class="form-input" accept=".jpg,.jpeg,.png,.gif">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Update Member</button>
            <a href="members.php" class="btn-secondary"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
