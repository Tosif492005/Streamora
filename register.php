<?php
// login.php
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($password === $row['password_hash']) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                redirect('index.php');
            } else {
                $error = 'Invalid password.';
            }
        } else {
            $error = 'Email not found.';
        }
    }
}

// We'll use a minimal header (no nav) for a clean login page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Streamora</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Login page specific overrides */
        body {
            background: #141414;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: rgba(0,0,0,0.75);
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.8);
            backdrop-filter: blur(4px);
        }
        .login-container .logo {
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: #e50914;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }
        .login-container h2 {
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .login-container .form-group {
            margin-bottom: 1.2rem;
        }
        .login-container .form-group label {
            display: block;
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .login-container .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            background: #333;
            color: #fff;
            font-size: 1rem;
            transition: background 0.3s;
        }
        .login-container .form-group input:focus {
            outline: none;
            background: #444;
        }
        .login-container .btn-login {
            width: 100%;
            padding: 0.8rem;
            background: #e50914;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 0.5rem;
        }
        .login-container .btn-login:hover {
            background: #b20710;
        }
        .login-container .error {
            background: #2a1212;
            color: #e50914;
            padding: 0.6rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
            border: 1px solid #e50914;
        }
        .login-container .extra {
            text-align: center;
            margin-top: 1.5rem;
            color: #aaa;
        }
        .login-container .extra a {
            color: #e50914;
            text-decoration: none;
        }
        .login-container .extra a:hover {
            text-decoration: underline;
        }
        /* Background image (optional) – you can set a hero image */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('assets/images/login-bg.jpg') no-repeat center center/cover;
            opacity: 0.3;
            z-index: -1;
        }
        /* If you don't have a background image, remove the ::before rule */
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">🎬 Streamora</div>
        <h2>Login</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="extra">
            Don't have an account? <a href="register.php">Register here</a>.
        </div>
    </div>
</body>
</html>