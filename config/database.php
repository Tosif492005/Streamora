<?php
// database.php
$host = 'localhost';
$user = 'root';
$pass = '';          // your MySQL password
$dbname = 'streamora';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");
?>
