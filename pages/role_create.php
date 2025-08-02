<?php
require_once '../configuration/config.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if (empty($name)) {
        $error = "Role name is required";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
            $stmt->execute([$name, $description]);
            $success = "Role created successfully";
            // Redirect after 2 seconds
            header("refresh:2;url=roles.php");
        } catch (PDOException $e) {
            $error = "Error creating role: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Role - Church Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/dashboard.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar">
        <div class="user-info">
            <a href="roles.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Roles
            </a>
        </div>
    </nav>

    <div class="container">
        <h1>Create New Role</h1>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <?= h($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= h($success) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="form">
            <div class="form-group">
                <label for="name">
                    <i class="fas fa-tag"></i>
                    Role Name
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       class="form-input" 
                       placeholder="Enter role name"
                       required>
            </div>

            <div class="form-group">
                <label for="description">
                    <i class="fas fa-align-left"></i>
                    Description
                </label>
                <textarea id="description" 
                          name="description" 
                          class="form-input" 
                          rows="4" 
                          placeholder="Enter role description"
                          required></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i>
                    Create Role
                </button>
                <a href="roles.php" class="btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>
        </form>
    </div>
</body>
</html>