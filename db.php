<?php
// db.php
// Database connection using mysqli

$host = 'localhost';
$db   = 'brainbridge';
$user = 'root'; // Change if your MySQL username is different
$pass = '';     // Change if your MySQL has a password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
