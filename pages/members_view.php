<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if (!isset($_GET['id'])) {
    redirect('members.php');
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT m.*, c.name AS church_name, r.name AS role_name FROM members m LEFT JOIN churches c ON m.church_id = c.id LEFT JOIN roles r ON m.role_id = r.id WHERE m.id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();

if (!$member) {
    redirect('members.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Member Details</title>
    <link rel="stylesheet" href="../css/dashboard.css" />
</head>
<body>
<nav class="navbar">
    <a href="members.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Members</a>
</nav>

<div class="container">
    <h1>Member Details</h1>

    <div style="text-align:center; margin-bottom:20px;">
       <img src="<?= h($member['photo'] ?: 'assets/images/default-profile.png') ?>" alt="Photo">
    </div>

    <table class="roles-table" style="max-width:600px; margin:auto;">
        <tr><th>Full Name</th><td><?= h($member['first_name'] . ' ' . $member['last_name']) ?></td></tr>
        <tr><th>Gender</th><td><?= h($member['gender']) ?></td></tr>
        <tr><th>Birth Date</th><td><?= h($member['birth_date']) ?></td></tr>
        <tr><th>Phone</th><td><?= h($member['phone']) ?></td></tr>
        <tr><th>Email</th><td><?= h($member['email']) ?></td></tr>
        <tr><th>Address</th><td>
            <?= h($member['address_line1']) ?><br/>
            <?= h($member['address_line2']) ?><br/>
            <?= h($member['city']) ?>
        </td></tr>
        <tr><th>Role</th><td><?= h($member['role_name']) ?></td></tr>
        <tr><th>Church</th><td><?= h($member['church_name']) ?></td></tr>
        <tr><th>Status</th><td><?= ucfirst(h($member['status'])) ?></td></tr>
        <tr><th>Member Since</th><td><?= h($member['created_at']) ?></td></tr>
    </table>
</div>
</body>
</html>
