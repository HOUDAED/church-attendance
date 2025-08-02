<?php
require_once 'configuration/config.php';
session_start();

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
        } catch (PDOException $e) {
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
  <style>
    :root {
      --bg-gradient: linear-gradient(-45deg, #ff7e5f, #feb47b, #6b34fe, #0f52ba);
      --panel-bg: rgba(255,255,255,0.15);
      --primary-gradient: linear-gradient(135deg, #6B34FE, #0F52BA);
      --highlight: rgba(255,215,0,0.8);
      --input-bg: rgba(255,255,255,0.2);
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body {
      font-family:'Montserrat',sans-serif;
      height:100vh; display:flex; align-items:center; justify-content:center;
      background:var(--bg-gradient); background-size:600% 600%;
      animation:bgAnim 20s ease infinite;
      position:relative; overflow:hidden;
    }
    @keyframes bgAnim {
      0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}
    }
    body::before {
      content:''; position:absolute; width:300px; height:300px;
      background:rgba(255,255,255,0.15); filter:blur(120px); border-radius:50%;
      top:10%; left:15%; animation:bokeh 25s ease infinite; z-index:0;
    }
    @keyframes bokeh {
      0%{transform:translate(0,0)}25%{transform:translate(60px,-40px)}50%{transform:translate(0,30px)}75%{transform:translate(-40px,0)}100%{transform:translate(0,0)}
    }
    body::after {
      content:''; position:absolute; inset:0;
      background:radial-gradient(circle at center, transparent 60%, rgba(0,0,0,0.6));
      pointer-events:none; z-index:2;
    }
    .login-container {
      position:relative; z-index:5;
      width:90%; max-width:420px; padding:2rem;
      background:var(--panel-bg); backdrop-filter:blur(15px);
      border:1px solid rgba(255,255,255,0.3); border-radius:32px;
      box-shadow:0 20px 40px rgba(0,0,0,0.3),0 0 60px rgba(255,255,255,0.1);
    }
    .login-container::after {
      content:''; position:absolute; inset:0;
      background:radial-gradient(circle at top, var(--highlight), transparent 80%);
      pointer-events:none;
    }
    .login-header { text-align:center; margin-bottom:2rem; }
    .login-header h1 {
      display:inline-block; font-family:'Playfair Display',serif; font-size:3rem;
      color:white; text-shadow:2px 4px 8px rgba(0,0,0,0.5);
    }
    .login-header p {
      margin-top:0.5rem; color:rgba(255,255,255,0.85); font-size:1.1rem;
    }
    .form-group {
      position:relative; margin-bottom:1.75rem;
    }
    .form-group i.fa-user,
    .form-group i.fa-lock {
      position:absolute; left:0.75rem; top:50%;
      transform:translateY(-50%); color:rgba(255,255,255,0.7);
      font-size:1.2rem; pointer-events:none; z-index:2;
    }
    .form-input {
      width:100%; padding:1rem 3rem 1rem 3rem;
      background:var(--input-bg); border:1px solid rgba(255,255,255,0.3);
      border-radius:12px; color:white; font-size:1rem;
      transition:all 0.3s ease; backdrop-filter:blur(5px);
    }
    .form-input::placeholder {
      color:rgba(255,255,255,0.85); font-style:italic;
    }
    .form-input:focus {
      outline:none; border-color:var(--highlight);
      box-shadow:0 0 8px 3px rgba(255,215,0,0.75);
      animation:inputPulse 1s ease infinite alternate;
    }
    @keyframes inputPulse {
      from { box-shadow:0 0 8px 3px rgba(255,215,0,0.75); }
      to   { box-shadow:0 0 12px 5px rgba(255,215,0,0.85); }
    }
    .password-toggle {
      position:absolute; right:0.75rem; top:50%;
      transform:translateY(-50%); color:rgba(255,255,255,0.7);
      cursor:pointer; font-size:1.2rem; z-index:2;
      transition:transform 0.3s ease,color 0.3s ease;
    }
    .password-toggle:hover {
      color:white; transform:scale(1.3);
    }
    .login-button {
      width:100%; padding:1rem; margin-top:0.5rem;
      background:var(--primary-gradient); color:white;
      border:none; border-radius:12px; font-size:1.2rem;
      font-weight:600; cursor:pointer; position:relative;
      overflow:hidden; transition:transform 0.3s ease;
    }
    .login-button::before {
      content:''; position:absolute; inset:-50%;
      background:radial-gradient(circle at center, rgba(255,255,255,0.3), transparent 70%);
      opacity:0; transition:opacity 0.5s ease;
    }
    .login-button:hover::before { opacity:1; }
    .login-button::after {
      content:''; position:absolute; width:100%; height:100%; top:0; left:-100%;
      background:linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition:left 0.5s ease;
    }
    .login-button:hover::after { left:100%; }
    .login-button:hover { transform:scale(1.05); }
    .error-message {
      background:linear-gradient(to right,#ff416c,#ff4b2b);
      color:white; padding:1rem; border-radius:12px;
      margin-bottom:1.5rem; text-align:center;
      animation:slideIn 0.3s ease;
    }
    @keyframes slideIn {
      from { transform:translateY(-10px); opacity:0;}
      to   { transform:translateY(0);    opacity:1;}
    }
    @media (max-width:480px) {
      .login-container { margin:1rem; }
      .login-header h1 { font-size:2rem; }
      .login-header p { font-size:1rem; }
    }
  </style>
</head>
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
