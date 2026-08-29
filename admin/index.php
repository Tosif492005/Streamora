<?php
// admin/index.php
session_start();

// If admin is already logged in, go to dashboard
if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

// Otherwise, redirect to login page
header('Location: login.php');
exit;
?>