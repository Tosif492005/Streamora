<?php
// database.php
$host = '193.203.184.233';
$user = 'u217687379_streamora';
$pass = 'Streamora@2026';          // your MySQL password
$dbname = 'u217687379_streamora';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");
?>
