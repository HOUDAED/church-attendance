<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$members = $pdo->query("
    SELECT m.*, r.name AS role_name, c.name AS church_name
    FROM members m
    LEFT JOIN roles r ON m.role_id = r.id
    LEFT JOIN churches c ON m.church_id = c.id
    ORDER BY m.created_at DESC
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Members - Church Management</title>
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
            <h1>Church Members</h1>
            <a href="member_create.php" class="add-role-btn">
                <i class="fas fa-plus"></i> Add New Member
            </a>
        </div>

        <div style="overflow-x:auto;">
            <table class="roles-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Church</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($members as $m): ?>
                        <tr>
                            <td><?= h($m['first_name'] . ' ' . $m['last_name']) ?></td>
                            <td><?= h($m['gender']) ?></td>
                            <td><?= h($m['phone']) ?></td>
                            <td><?= h($m['email']) ?></td>
                            <td><?= h($m['role_name']) ?></td>
                            <td><?= h($m['church_name']) ?></td>
                            <td><?= ucfirst(h($m['status'])) ?></td>
                            <td>
                                <div class="role-actions">
                                    <button onclick="location.href='member_view.php?id=<?= h($m['id']) ?>'" class="action-icon-btn view-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="location.href='member_edit.php?id=<?= h($m['id']) ?>'" class="action-icon-btn edit-btn" title="Edit Member">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="member_delete.php" onsubmit="return confirm('Delete this member?');" style="display:inline;">
                                        <input type="hidden" name="id" value="<?= h($m['id']) ?>">
                                        <button type="submit" class="action-icon-btn delete-btn" title="Delete Member">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
