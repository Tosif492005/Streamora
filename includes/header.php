<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Streamora</title>
    <link rel="stylesheet" href="assets/css/style.css">

    
<link rel="icon" type="image/x-icon" href="favicon.ico">

<li><a href="contact.php">Contact</a></li>


</head>
<body>

  <!-- includes/header.php -->
<header>
    <nav>
        <div class="logo"><a href="index.php">🎬 Streamora</a></div>
        
        <!-- NEW SEARCH BAR -->
        <div class="search-container">
            <form action="search.php" method="GET">
                <input type="text" name="q" placeholder="Search for movies..." required>
                <button type="submit">🔍</button>
            </form>
        </div>

        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="wishlist.php">❤️ Wishlist</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>


