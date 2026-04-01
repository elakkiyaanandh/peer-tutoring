<?php
// register_process.php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    // Hash the password for security
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // SQL query to check if email already exists
    $check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    // Check if any rows are returned
    if ($check_stmt->num_rows > 0) {
        echo "Email already registered. <a href='login.html'>Login here</a>";
    } else {
        // Insert new user into the database
        $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("sss", $name, $email, $password);
        
        if ($insert_stmt->execute()) {
            echo "Registration successful! <a href='login.html'>Login here</a>";
        } else {
            echo "Error: " . $insert_stmt->error;
        }
        $insert_stmt->close();
    }
    
    $check_stmt->close();
}
$conn->close();
?>
