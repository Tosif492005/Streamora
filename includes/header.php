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

    <!-- Favicon - try a simple relative path first -->
    <link rel="icon" type="image/x-icon" href="favicon.ico">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <!-- Fallback: inline base64 favicon -->
    <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAARzQklUCAgICHwIZAAAAAlwSFlzAAAOxAAADsQBlSsOGwAAAcpJREFUOE+lUz1oE1EYPveSS5toixTq4CgKXZRuhUQhEYoU/BkUodQfwR+odLCLiktxqVQkk/ilxclScx+o6CQlS4OdiJ2aotDBUMGWDh2eTnbxPU+a95rGJoR74e79vu/7nvd97wfAL/ndY8zGDRPjKDknETk6Vk5BmCJICJOA9BcYJ7RUBTIFyEf3C4U92K5Ti6hUipO1Wm22Xq9fLpVKZ5Hps3rGGIC/7jTGxgP0eGxk7NECllsL+FjuwQjSx7UFhmcoQc4zFmEwjlVWwzpv41M7gBZtIkc9ZgPj8Q2MT/0H1spjeYtRGX3SRJmYqYkO5qjHOhsi99OFYGG99BK3WzXshd34Qgfwkp42BaHjGSf6KNS6UMP6IXnrVcRsJcaenH1lCky3pmjDe1cOK/54lPmw9Ql5qtOX45yJjImJS0dLcJcVY6NAqE4wntoo81CwHh/hJ6cGfBDXl29imYzQj3jw6mNE+5KdMj8M+f7cK3nkdt2tdmCgUXbQOsTpYhYlx4Hh6iiB46RMk3lazibDcXM7z5hzN6jXp8LRtzu6PB7mne7y7JmH9R/WEDDjjDQ3p/8vgWZ2CvLTW8tvWrVHB2e/AJvz6fErVrFiAAAAAElFTkSuQmCC">

    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo"><a href="index.php">🎬 Streamora</a></div>
            
            <!-- Search bar -->
            <div class="search-container">
                <form action="search.php" method="GET">
                    <input type="text" name="q" placeholder="Search for movies..." required>
                    <button type="submit">🔍</button>
                </form>
            </div>

            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="contact.php">📧 Contact</a></li>   <!-- Contact link is now correctly placed -->
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
    <main>