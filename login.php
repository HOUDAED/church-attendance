<?php
require_once 'configuration/config.php';

// Enable error display for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        try {
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user) {
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    redirect('pages/dashboard.php');
                } else {
                    $error = "Incorrect password";
                }
            } else {
                $error = "User not found";
            }
        } catch(PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Login - Church Attendance System</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Montserrat:wght@300;500;700&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/login.css">

<body>
  <div class="login-container">
    <div class="login-header">
      <h1>Welcome</h1>
      <p>Sign in to your account</p>
    </div>

    <?php if ($error): ?>
      <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" autocomplete="off">
      <div class="form-group">
        <i class="fas fa-user"></i>
        <input type="text" name="username" class="form-input" placeholder="Username" required>
      </div>
      <div class="form-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" class="form-input" placeholder="Password" required>
        <i class="fas fa-eye password-toggle" onclick="togglePassword(this)"></i>
      </div>
      <button type="submit" class="login-button">Sign In</button>
    </form>
  </div>

  <script>
    function togglePassword(el) {
      const inp = el.previousElementSibling;
      if (inp.type === 'password') {
        inp.type = 'text';
        el.classList.replace('fa-eye','fa-eye-slash');
      } else {
        inp.type = 'password';
        el.classList.replace('fa-eye-slash','fa-eye');
      }
    }
  </script>
</body>
</html>