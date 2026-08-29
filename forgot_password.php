<?php
// forgot_password.php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'config/email.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Check if user exists
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Save token in database (create password_resets table)
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user['id'], $token, $expires);
            $stmt->execute();
            
            // Send reset email
            $resetLink = "http://localhost/Streamora/reset_password.php?token=" . $token;
            $body = "
            <h2>Password Reset Request</h2>
            <p>Hi {$user['username']},</p>
            <p>Click the link below to reset your password:</p>
            <p><a href='{$resetLink}'>Reset Password</a></p>
            <p>This link expires in 1 hour.</p>
            ";
            
            $result = sendEmail($email, 'Password Reset - Streamora', $body);
            
            if ($result['success']) {
                $message = 'Password reset link sent to your email.';
            } else {
                $error = 'Failed to send email: ' . $result['message'];
            }
        } else {
            $message = 'If this email exists, a reset link will be sent.';
        }
    }
}

include 'includes/header.php';
?>

<h2>Forgot Password</h2>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post">
    <div class="form-group">
        <label>Email Address</label>
        <input type="email" name="email" required>
    </div>
    <button type="submit" class="btn">Send Reset Link</button>
</form>

<?php include 'includes/footer.php'; ?>