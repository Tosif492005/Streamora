<?php
// contact.php
require_once 'config/database.php';
require_once 'includes/functions.php';
require_once 'config/email.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $msg = trim($_POST['message'] ?? '');
    
    if (empty($name) || empty($email) || empty($subject) || empty($msg)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        // Prepare email content[reference:9]
        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $safeSubject = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
        $safeMsg = nl2br(htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'));
        
        $body = "
        <h2>New Contact Form Message</h2>
        <p><strong>Name:</strong> {$safeName}</p>
        <p><strong>Email:</strong> {$safeEmail}</p>
        <p><strong>Subject:</strong> {$safeSubject}</p>
        <p><strong>Message:</strong><br>{$safeMsg}</p>
        ";
        
        $result = sendEmail(SMTP_FROM_EMAIL, "Contact: $subject", $body);
        
        if ($result['success']) {
            $message = 'Thank you! Your message has been sent.';
        } else {
            $error = 'Failed to send: ' . $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<h2>Contact Us</h2>

<?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="post" class="contact-form">
    <div class="form-group">
        <label>Your Name</label>
        <input type="text" name="name" required>
    </div>
    <div class="form-group">
        <label>Your Email</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Subject</label>
        <input type="text" name="subject" required>
    </div>
    <div class="form-group">
        <label>Message</label>
        <textarea name="message" rows="5" required></textarea>
    </div>
    <button type="submit" class="btn">Send Message</button>
</form>

<?php include 'includes/footer.php'; ?>