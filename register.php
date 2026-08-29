<?php
// register.php
require_once 'config/database.php';
require_once 'includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PHPMailer includes
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];

    // --- Validation ---
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } else {
        // Check for duplicate username/email
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Username or email already taken.';
        } else {
            // ⚠️ NO HASHING – password stored as plain text
            $plain_password = $password;  // or $password_confirm

            $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $plain_password);

            if ($stmt->execute()) {
                $user_id = $stmt->insert_id;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['username'] = $username;

                // ----- SEND WELCOME EMAIL (PHPMailer) -----
                try {
                    $mail = new PHPMailer(true);
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'tosifgadgekar2005@gmail.com';
                    $mail->Password   = 'juzna judby jryatq';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('tosifgadgekar2005@gmail.com', 'Streamora Team');
                    $mail->addAddress($email, $username);
                    $mail->addReplyTo('tosifgadgekar2005@gmail.com');

                    $mail->isHTML(true);
                    $mail->Subject = 'Welcome to Streamora!';
                    $mail->Body    = "
                        <h2>Welcome, $username!</h2>
                        <p>Thank you for registering at <strong>Streamora</strong>.</p>
                        <p>You can now log in and start exploring.</p>
                        <p>– The Streamora Team</p>
                    ";
                    $mail->AltBody = "Welcome, $username!\nThank you for registering at Streamora.\n– The Streamora Team";

                    $mail->send();
                } catch (Exception $e) {
                    error_log("Welcome email failed: " . $mail->ErrorInfo);
                }

                redirect('index.php');
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!-- HTML form remains exactly the same as before -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register – Streamora</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Same styles as login page – keep consistent */
        body {
            background: #141414;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .register-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: rgba(0,0,0,0.75);
            border-radius: 8px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.8);
            backdrop-filter: blur(4px);
        }
        .register-container .logo {
            text-align: center;
            font-size: 2.5rem;
            font-weight: bold;
            color: #e50914;
            margin-bottom: 1.5rem;
            letter-spacing: -1px;
        }
        .register-container h2 {
            color: #fff;
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .register-container .form-group {
            margin-bottom: 1.2rem;
        }
        .register-container .form-group label {
            display: block;
            color: #aaa;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        .register-container .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: none;
            border-radius: 4px;
            background: #333;
            color: #fff;
            font-size: 1rem;
            transition: background 0.3s;
        }
        .register-container .form-group input:focus {
            outline: none;
            background: #444;
        }
        .register-container .btn-register {
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
        .register-container .btn-register:hover {
            background: #b20710;
        }
        .register-container .error {
            background: #2a1212;
            color: #e50914;
            padding: 0.6rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            text-align: center;
            border: 1px solid #e50914;
        }
        .register-container .extra {
            text-align: center;
            margin-top: 1.5rem;
            color: #aaa;
        }
        .register-container .extra a {
            color: #e50914;
            text-decoration: none;
        }
        .register-container .extra a:hover {
            text-decoration: underline;
        }
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
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">🎬 Streamora</div>
        <h2>Create Account</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Your unique username" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="••••••••" required>
            </div>
            <div class="form-group">
                <label for="password_confirm">Confirm Password</label>
                <input type="password" name="password_confirm" id="password_confirm" placeholder="Confirm your password" required>
            </div>
            <button type="submit" class="btn-register">Sign Up</button>
        </form>

        <div class="extra">
            Already have an account? <a href="login.php">Login here</a>.
        </div>
    </div>
</body>
</html>