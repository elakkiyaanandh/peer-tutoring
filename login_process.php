<?php
// login_process.php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Select the user based on the email provided
    $stmt = $conn->prepare("SELECT user_id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Check if the user exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            // Set session variables upon successful login
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            
            // Redirect to the dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Incorrect password. <a href='login.html'>Try again</a>";
        }
    } else {
        echo "Email not found. <a href='register.html'>Register here</a>";
    }
    
    $stmt->close();
}
$conn->close();
?>
