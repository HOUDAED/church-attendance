<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

// Handle church deletion
if (isset($_POST['delete_church'])) {
    $church_id = $_POST['delete_church'];
    $stmt = $pdo->prepare("DELETE FROM churches WHERE id = ?");
    $stmt->execute([$church_id]);
    redirect('churches.php');
}

// Fetch all churches
$churches = $pdo->query("SELECT * FROM churches ORDER BY name")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Churches Management - Church Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/churches.css" rel="stylesheet">
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

    <div class="container">
        <div class="header-actions">
            <h1>Churches Management</h1>
            <a href="church_create.php" class="btn-primary">
                <i class="fas fa-plus"></i> Add New Church
            </a>
        </div>

        <div class="table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Location</th>
                        <th>Contact Phone</th>
                        <th>Actions</th>
                        <th>Image</th>
                        <th>Email</th>
                        <th>Description</th>
                        <th>Founded</th>

                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($churches as $church): ?>
                    <tr>
                        <td><?= h($church['id']) ?></td>
                        <td><?= h($church['name']) ?></td>
                        <td><?= h($church['location']) ?></td>
                        <td><?= h($church['contact_phone']) ?></td>
                        <td>
                            <?php if ($church['image']): ?>
                                <img src="../<?= h($church['image']) ?>" alt="Church Image" style="height: 40px; border-radius: 4px;">
                            <?php endif; ?>
                        </td>
                        <td><?= h($church['contact_email']) ?></td>
                        <td><?= h($church['description']) ?></td>
                        <td><?= h($church['foundation_date']) ?></td>
                        <td class="actions">
                            <a href="church_edit.php?id=<?= h($church['id']) ?>" class="btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" class="delete-form" style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to delete this church?');">
                                <button type="submit" name="delete_church" 
                                        value="<?= h($church['id']) ?>" 
                                        class="btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>