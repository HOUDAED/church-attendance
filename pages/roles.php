<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

// Handle role deletion
if (isset($_POST['delete_role'])) {
    $role_id = $_POST['delete_role'];
    $stmt = $pdo->prepare("DELETE FROM roles WHERE id = ?");
    $stmt->execute([$role_id]);
    redirect('roles.php');
}

// Fetch all roles
$roles = $pdo->query("SELECT * FROM roles ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Role Management - Church Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="user-info">
            <a href="dashboard.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>
        <a href="logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </nav>

    <div class="roles-container">
        <div class="roles-header">
            <h1>Role Management</h1>
            <a href="role_create.php" class="add-role-btn">
                <i class="fas fa-plus"></i>
                Add New Role
            </a>
        </div>

        <table class="roles-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Role Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($roles as $role): ?>
                <tr>
                    <td><?= h($role['id']) ?></td>
                    <td><?= h($role['name']) ?></td>
                    <td><?= h($role['description']) ?></td>
                    <td>
                        <div class="role-actions">
                            <button class="action-icon-btn edit-btn" 
                                    onclick="location.href='role_edit.php?id=<?= h($role['id']) ?>'">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" class="delete-form" style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this role?');">
                                <button type="submit" name="delete_role" 
                                        value="<?= h($role['id']) ?>" 
                                        class="action-icon-btn delete-btn">
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