<?php
// admin/login.php
session_start();
require_once '../config/database.php';

// If already logged in as admin, redirect to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        // Query the admins table
        $stmt = $conn->prepare("SELECT id, username, password, role FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin) {
            // Direct plain text comparison
            if ($password === $admin['password']) {
                // Login successful
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_role'] = $admin['role'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid password.';
            }
        } else {
            $error = 'Admin email not found.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – Streamora</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Same styles as before – copy from previous answer */
        body {
            background: #0a0a0a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }
        .admin-login {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: rgba(0,0,0,0.85);
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.8);
        }
        .admin-login .logo {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            color: #e50914;
            margin-bottom: 0.5rem;
        }
        .admin-login .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        .admin-login h2 {
            color: #fff;
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .admin-login .form-group {
            margin-bottom: 1rem;
        }
        .admin-login .form-group label {
            display: block;
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .admin-login .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            background: #333;
            color: #fff;
            font-size: 1rem;
        }
        .admin-login .form-group input:focus {
            outline: none;
            background: #444;
        }
        .admin-login .btn-login {
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
        }
        .admin-login .btn-login:hover {
            background: #b20710;
        }
        .admin-login .error {
            background: #2a1212;
            color: #e50914;
            padding: 0.6rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
            border: 1px solid #e50914;
        }
        .admin-login .back-link {
            text-align: center;
            margin-top: 1rem;
            color: #666;
        }
        .admin-login .back-link a {
            color: #e50914;
            text-decoration: none;
        }
        .admin-login .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="admin-login">
        <div class="logo">🎬 Streamora</div>
        <div class="subtitle">Admin Panel</div>
        <h2>Login</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="admin@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-login">Login to Admin</button>
        </form>

        <div class="back-link">
            <a href="../index.php">← Back to Website</a>
        </div>
    </div>
</body>
</html>